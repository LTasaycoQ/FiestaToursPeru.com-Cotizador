<?php

namespace App\Exports;

use App\Models\Quote;
use App\Models\Service;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Style\Language;

class QuoteDocxExport
{
    public function __construct(
        private readonly int $quoteId,
        private readonly string $dayTitleColor = '275317',
    ) {}

    public function download()
    {
        $quote = Quote::with([
            'client',
            'contact',
            'language',
            'quoteDays.details.service.descriptions.language',
        ])->findOrFail($this->quoteId);

        $phpWord = new PhpWord;
        $phpWord->getSettings()->setThemeFontLang(new Language(Language::ES_ES));
        $section = $phpWord->addSection([
            'marginLeft' => 1580,
            'marginRight' => 1580,
            'marginTop' => 600,
            'marginBottom' => 600,
        ]);

        $section->addTitle('Cotización '.($quote->quote_number ?? $quote->id_quote), 1);
        $section->addText('Cliente: '.($quote->client?->name_client ?? 'No hay pe causa'));
        $section->addText('Contacto: '.($quote->contact ? trim(($quote->contact->name ?? '').' '.($quote->contact->last_names ?? '')) : 'No hay pe causa'));

        if ($quote->start_date || $quote->end_date) {
            $section->addText('Fechas: '.$this->formatShortDate($quote->start_date).' - '.$this->formatShortDate($quote->end_date));
        }

        if (! empty($quote->notes)) {
            $section->addTextBreak();
            $section->addText('Observaciones:', ['bold' => true]);
            $section->addText($quote->notes);
        }

        $section->addTextBreak();

        $days = $quote->quoteDays()->orderBy('day_number')->with('details.service.descriptions.language')->get();

        foreach ($days as $day) {
            $section->addText(
                'Día '.$day->day_number.', '.$this->formatShortDate($day->date),
                [
                    'bold' => true,
                    'size' => 11,
                    'color' => $this->dayTitleColor,
                    'indentLeft' => 0,
                ]
            );

            $details = $day->details()->orderBy('id_detail_quote')->get();

            if ($details->isEmpty()) {
                $section->addText('');

                continue;
            }

            foreach ($details as $detail) {
                $serviceName = $detail->service?->name_service ?? 'Servicio eliminado';
                $description = $this->extractDescription($detail->service, $quote->id_language);
                $notes = trim((string) ($detail->notes ?? ''));

                $table = $section->addTable([
                    'borderSize' => 6,
                    'borderColor' => 'FFFFFF',
                    'cellMargin' => 0,
                    'width' => 120,
                    'unit' => 'pct',
                    'alignment' => 'left',
                ]);
                
                $table->addRow();

                $serviceCell = $table->addCell(10000);
                $serviceCell->addText($serviceName, ['bold' => true, 'size' => 10, 'color' => $this->dayTitleColor]);

                if (trim((string) $description) !== '') {
                    $serviceCell->addText($description);
                }

                if ($notes !== '') {
                    $serviceCell->addText($notes, ['bold' => true, 'color' => 'C90202', 'size' => 9]);
                }

                $principalImage = $detail->service?->principalImage()->first();
                $imagePath = $principalImage?->image_path ?? ($detail->service?->imagen ? $detail->service->imagen : null);

                if ($imagePath) {
                    $absoluteImagePath = Storage::disk('public')->path($imagePath);
                    if (file_exists($absoluteImagePath)) {
                        $serviceCell->addTextBreak();
                        $serviceCell->addImage($absoluteImagePath, [
                            'width' => 420,
                            'height' => 90,
                            'wrappingStyle' => 'square',
                            'position' => 'relative',
                            'alignment' => 'center',
                        ]);
                    }
                }

                $section->addTextBreak(0.5);

            }

            $section->addTextBreak();

        }


        $tempPath = Storage::disk('local')->path('temp/quote-'.$this->quoteId.'.docx');
        $directory = dirname($tempPath);
        if (! is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        

        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tempPath);

        return response()->download($tempPath, 'cotizacion-'.$this->quoteId.'.docx')->deleteFileAfterSend(true);
    }

    private function extractDescription(?Service $service, ?int $languageId = null): string
    {
        if (! $service) {
            return '';
        }

        $languageDescription = $languageId
            ? $service->descriptions()->where('id_language', $languageId)->first()?->description
            : null;

        if (is_string($languageDescription) && trim($languageDescription) !== '') {
            return trim($languageDescription);
        }

        if (! empty($service->description)) {
            return trim((string) $service->description);
        }

        $description = $service->descriptions()->first()?->description;

        return trim((string) ($description ?? ''));
    }

    private function formatShortDate(mixed $date): string
    {
        if (! $date) {
            return 'Sin definir';
        }

        $months = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
        ];

        return $months[(int) $date->format('n')].' '.$date->format('j');
    }
}
