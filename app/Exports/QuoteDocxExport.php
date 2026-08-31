<?php

namespace App\Exports;

use App\Models\Quote;
use App\Models\Service;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;

class QuoteDocxExport
{
    public function __construct(private readonly int $quoteId) {}

    public function download()
    {
        $quote = Quote::with([
            'client',
            'contact',
            'quoteDays.details.service.descriptions.language',
        ])->findOrFail($this->quoteId);

        $phpWord = new PhpWord();
        $section = $phpWord->addSection([
            'marginLeft' => 600,
            'marginRight' => 600,
            'marginTop' => 600,
            'marginBottom' => 600,
        ]);

        $section->addTitle('Cotización '.($quote->quote_number ?? $quote->id_quote), 1);
        $section->addText('Cliente: '.($quote->client?->name_client ?? 'N/A'));
        $section->addText('Contacto: '.($quote->contact ? trim(($quote->contact->name ?? '').' '.($quote->contact->last_names ?? '')) : 'N/A'));

        if ($quote->start_date || $quote->end_date) {
            $section->addText('Fechas: '.($quote->start_date ? $quote->start_date->format('d/m/Y') : 'Sin definir').' - '.($quote->end_date ? $quote->end_date->format('d/m/Y') : 'Sin definir'));
        }

        if (! empty($quote->notes)) {
            $section->addTextBreak();
            $section->addText('Observaciones:', ['bold' => true]);
            $section->addText($quote->notes);
        }

        $section->addTextBreak();

        $days = $quote->quoteDays()->orderBy('day_number')->with('details.service.descriptions.language')->get();

        foreach ($days as $day) {
            $section->addText('Día '.$day->day_number.($day->date ? ' - '.$day->date->format('d/m/Y') : ''), ['bold' => true, 'size' => 16]);

            $details = $day->details()->orderBy('id_detail_quote')->get();

            if ($details->isEmpty()) {
                $section->addText('');
                continue;
            }

            foreach ($details as $detail) {
                $serviceName = $detail->service?->name_service ?? 'Servicio eliminado';
                $section->addText($serviceName, ['bold' => true, 'size' => 12]);

                $description = $this->extractDescription($detail->service);
                if (trim((string) $description) === '') {
                    $section->addText('');
                    continue;
                }

                $section->addText($description);
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

    private function extractDescription(?Service $service): string
    {
        if (! $service) {
            return '';
        }

        if (! empty($service->description)) {
            return trim((string) $service->description);
        }

        $description = $service->descriptions()->first()?->description;

        return trim((string) ($description ?? ''));
    }
}
