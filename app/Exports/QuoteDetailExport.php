<?php

namespace App\Exports;

use App\Models\Quote;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class QuoteDetailExport implements FromCollection, WithEvents, WithHeadings, WithStyles, WithTitle
{
    public function __construct(private readonly int $quoteId) {}

    public function title(): string
    {
        return 'Servicios';
    }

    public function headings(): array
    {
        $quote = Quote::with('client')->findOrFail($this->quoteId);

        return [
            // ['FIESTA TOURS PERU', '', '', ''],
            // ['Av San Luis 2644 San Borja T: +51-1 225-1336', '', '', ''],

            // ['Para:', $quote->client->name_client ?? 'No Definido', '', ''],
            // ['REF:', $quote->name ?? 'No Definido', '', ''],
            ['FECHA:', $this->formatExportDate(now()), '', ''],
            ['', '', '', ''],
            ['', '', '', ''],
            ['FECHA', 'CIUDAD', 'SERVICIO', 'PRECIO'],
        ];
    }

    public function collection(): Collection
    {
        $quote = Quote::with([
            'quoteDays.details.service.tariffs.subCategory',
            'quoteDays.details.tariff.subCategory',
            'accommodations.quoteDay',
            'accommodations.service.supplier.city',
            'accommodations.tariff.subCategory',
        ])->findOrFail($this->quoteId);

        $rows = collect();
        $grandSubtotal = 0;
        $passengers = (int) ($quote->passengers_count ?: 1);

        foreach ($quote->quoteDays->sortBy('day_number') as $day) {
            foreach ($day->details->sortBy('id_detail_quote') as $detail) {
                if ($detail->is_optional) {
                    continue;
                }
                $serviceName = $detail->service?->name_service ?? 'Servicio eliminado';
                $unitPrice = (float) ($detail->unit_price ?? 0);

                $rows->push([
                    $this->formatExportDate($day->date, $day->day_number),
                    $day->name,
                    $serviceName,
                    $unitPrice,

                ]);

                $grandSubtotal += $unitPrice;
            }
        }

        $rows->push(['', '', 'Servicios, precio neto por persona no comisionable:', $grandSubtotal]);
        $rows->push(['', '', '', $passengers]);
        $rows->push(['', '', 'Sub Total Servicios', $grandSubtotal * $passengers]);

        $accommodationsByOption = $quote->accommodations
            ->sortBy([
                ['option_number', 'asc'],
                ['quoteDay.day_number', 'asc'],
                ['id_quote_accommodation', 'asc'],
            ])
            ->groupBy(fn ($accommodation) => (int) ($accommodation->option_number ?: 1));

        foreach ($accommodationsByOption as $optionNumber => $accommodations) {
            $roomCodes = array_values(array_filter(
                $this->hotelRoomCodes($accommodations),
                fn (string $roomCode): bool => $accommodations->contains(
                    fn ($accommodation): bool => $this->hotelRoomTypeCode(
                        $accommodation->room_type,
                        $accommodation->tariff?->subCategory?->name
                    ) === $roomCode
                )
            ));
            $rows->push(['', '', '', '']);
            $rows->push(['FECHA', 'NTS', 'CIUDAD', 'HOTEL', '', '', '', ...$roomCodes, 'TOTAL']);

            $optionSubtotal = 0;
            $accommodations->groupBy(fn ($accommodation) => implode(':', [
                $accommodation->id_service,
                $accommodation->id_season ?? $accommodation->tariff?->id_season ?? 'base',
            ]))->each(function (Collection $hotelRows) use (&$rows, &$optionSubtotal, $roomCodes): void {
                $accommodation = $hotelRows->first();
                $amounts = $hotelRows
                    ->groupBy(fn ($row) => $this->hotelRoomTypeCode($row->room_type, $row->tariff?->subCategory?->name))
                    ->map(fn (Collection $rooms, string $roomCode): float => (float) $rooms->sum(
                        fn ($room) => (int) ($room->room_count ?? 0)
                            * $this->hotelRoomCapacity($roomCode)
                            * (float) ($room->unit_price ?? 0)
                    ));
                $subtotal = (float) $amounts->sum();
                $optionSubtotal += $subtotal;

                $rows->push([
                    $this->formatExportDate($hotelRows->first()->quoteDay?->date),
                    $hotelRows->pluck('id_quote_day')->unique()->count(),
                    $accommodation->service?->supplier?->city?->name ?? 'Ciudad no especificada',
                    $this->hotelCell($accommodation),
                    '',
                    '',
                    '',
                    ...collect($roomCodes)->map(fn (string $roomCode) => $amounts->get($roomCode, 0))->all(),
                    $subtotal,
                ]);
            });

            $rows->push([
                '', '', '', '', '', '', '',
                ...array_fill(0, count($roomCodes), ''),
                'TOTAL OPCIÓN '.$optionNumber,
                $optionSubtotal,
            ]);
        }

        return $rows;
    }

    private function hotelCell(mixed $accommodation): RichText
    {
        $hotelName = $accommodation->service?->supplier?->supplier_name ?? 'Hotel no especificado';
        $serviceName = $accommodation->service?->name_service ?? 'Servicio eliminado';
        $richText = new RichText;
        $richText->createTextRun($hotelName)->getFont()->setBold(true);
        $richText->createTextRun(' - '.$serviceName)->getFont()->setSize(9)->setColor(new Color('7191A8'));

        return $richText;
    }

    private function hotelRoomTypeCode(?string $roomType, ?string $tariffName): string
    {
        $value = mb_strtolower(($roomType ?? '').' '.($tariffName ?? ''));

        return str_contains($value, 'tpl') || str_contains($value, 'triple')
            ? 'TPL'
            : (str_contains($value, 'dbl') || str_contains($value, 'doble') || str_contains($value, 'double')
                ? 'DBL'
                : 'SPL');
    }

    private function hotelRoomCapacity(string $roomCode): int
    {
        return match ($roomCode) {
            'DBL' => 2,
            'TPL' => 3,
            default => 1,
        };
    }

    private function hotelRoomCodes(Collection $accommodations): array
    {
        $codes = $accommodations
            ->map(fn ($accommodation) => $this->hotelRoomTypeCode(
                $accommodation->room_type,
                $accommodation->tariff?->subCategory?->name
            ))
            ->filter(fn (string $code) => in_array($code, ['SPL', 'DBL', 'TPL'], true))
            ->unique()
            ->values()
            ->all();

        return array_values(array_intersect(['SPL', 'DBL', 'TPL'], $codes));
    }

    private function formatExportDate(mixed $date, ?int $dayNumber = null): string
    {
        if (! $date) {
            return $dayNumber ? 'Día '.$dayNumber : 'Día 1';
        }

        $months = [
            1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
            5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
            9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre',
        ];

        return $date->format('d').'-'.$months[(int) $date->format('n')];
    }

    public function styles(Worksheet $sheet): array
    {

        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'D9EAF7']],
            ],
            3 => [
                'font' => ['bold' => true, 'size' => 13],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'E2F0D9']],
            ],
            4 => [
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ],
            'A1:F'.$sheet->getHighestRow() => [
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event): void {
                $sheet = $event->sheet->getDelegate();
                foreach ($sheet->getRowIterator() as $row) {
                    $rowNumber = $row->getRowIndex();
                    $value = (string) $sheet->getCell('A'.$rowNumber)->getValue();
                    $columnB = $sheet->getCell('B'.$rowNumber)->getValue();
                    if ($value === 'FECHA') {
                        $headerEnd = 'D';
                        for ($columnIndex = 1; $columnIndex <= 11; $columnIndex++) {
                            if ($sheet->getCellByColumnAndRow($columnIndex, $rowNumber)->getValue() === 'TOTAL') {
                                $headerEnd = $sheet->getCellByColumnAndRow($columnIndex, $rowNumber)->getColumn();
                                break;
                            }
                        }

                        $sheet->getStyle('A'.$rowNumber.':'.$headerEnd.$rowNumber)->applyFromArray([
                            'font' => ['bold' => true],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                        ]);
                        $sheet->getStyle('A'.$rowNumber.':'.$headerEnd.$rowNumber)
                            ->getBorders()->getAllBorders()
                            ->setBorderStyle(Border::BORDER_THIN)
                            ->getColor()->setRGB('000000');
                        $priceStart = $columnB === 'NTS' ? 'H' : 'D';
                        $sheet->getStyle($priceStart.$rowNumber.':'.$headerEnd.$rowNumber)
                            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                        if ($columnB === 'NTS') {
                            $sheet->mergeCells('D'.$rowNumber.':G'.$rowNumber);
                        }
                    }
                }

                // Ajusta aquí los anchos de las columnas del Excel (aprox. 7 px por unidad).
                $sheet->getColumnDimension('A')->setWidth(14);
                $sheet->getColumnDimension('B')->setWidth(14); // Ciudad en servicios / NTS en hoteles.
                $sheet->getColumnDimension('C')->setAutoSize(false);
                $sheet->getColumnDimension('C')->setWidth(35); // Servicio: aprox. 280 px.
                $sheet->getColumnDimension('D')->setWidth(11);
                $sheet->getColumnDimension('E')->setWidth(11);
                $sheet->getColumnDimension('F')->setWidth(11);
                $sheet->getColumnDimension('G')->setWidth(11);
                $sheet->getColumnDimension('H')->setWidth(13); // SPL.
                $sheet->getColumnDimension('I')->setWidth(13); // DBL.
                $sheet->getColumnDimension('J')->setWidth(13); // TPL.
                $sheet->getColumnDimension('K')->setWidth(14); // TOTAL.
                $sheet->getStyle('A1:K'.$sheet->getHighestRow())
                    ->getAlignment()
                    ->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A1:K'.$sheet->getHighestRow())
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('C1:G'.$sheet->getHighestRow())
                    ->getFont()->setSize(10);
                $sheet->getStyle('C1:G'.$sheet->getHighestRow())
                    ->getAlignment()
                    ->setWrapText(true)
                    ->setVertical(Alignment::VERTICAL_CENTER);

                foreach ($sheet->getRowIterator() as $row) {
                    $rowNumber = $row->getRowIndex();
                    $columnB = $sheet->getCell('B'.$rowNumber)->getValue();
                    $columnC = $sheet->getCell('C'.$rowNumber)->getValue();

                    $sheet->getRowDimension($rowNumber)->setRowHeight(22);

                    if ($columnB === 'NTS') {
                        $headerEnd = 'D';
                        for ($columnIndex = 1; $columnIndex <= 11; $columnIndex++) {
                            if ($sheet->getCellByColumnAndRow($columnIndex, $rowNumber)->getValue() === 'TOTAL') {
                                $headerEnd = $sheet->getCellByColumnAndRow($columnIndex, $rowNumber)->getColumn();
                                break;
                            }
                        }

                        $sheet->getStyle('A'.$rowNumber.':'.$headerEnd.$rowNumber)
                            ->getBorders()->getAllBorders()
                            ->setBorderStyle(Border::BORDER_THIN)
                            ->getColor()->setRGB('000000');
                        $sheet->getStyle('A'.$rowNumber.':'.$headerEnd.$rowNumber)
                            ->getAlignment()
                            ->setHorizontal(Alignment::HORIZONTAL_LEFT);
                        $sheet->getStyle('H'.$rowNumber.':'.$headerEnd.$rowNumber)
                            ->getAlignment()
                            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                        continue;
                    }

                    if (is_numeric($columnB)) {
                        $sheet->mergeCells('D'.$rowNumber.':G'.$rowNumber);
                        $sheet->getRowDimension($rowNumber)->setRowHeight(-1);
                        $sheet->getStyle('D'.$rowNumber.':G'.$rowNumber)
                            ->getAlignment()
                            ->setWrapText(true)
                            ->setVertical(Alignment::VERTICAL_CENTER);
                        $sheet->getStyle('H'.$rowNumber.':K'.$rowNumber)
                            ->getAlignment()
                            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                            ->setVertical(Alignment::VERTICAL_CENTER);
                    } elseif ($columnC) {
                        $sheet->getStyle('D'.$rowNumber)
                            ->getAlignment()
                            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                            ->setVertical(Alignment::VERTICAL_CENTER);
                    }

                    if ($sheet->getCell('A'.$rowNumber)->getValue() === ''
                        && $sheet->getCell('B'.$rowNumber)->getValue() === ''
                        && $sheet->getCell('C'.$rowNumber)->getValue() === ''
                        && is_numeric($sheet->getCell('D'.$rowNumber)->getValue())) {
                        $sheet->getStyle('D'.$rowNumber)
                            ->getAlignment()
                            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                            ->setVertical(Alignment::VERTICAL_CENTER);
                    }

                    $serviceSummary = (string) $sheet->getCell('C'.$rowNumber)->getValue();
                    if (in_array($serviceSummary, [
                        'Servicios, precio neto por persona no comisionable:',
                        'Sub Total Servicios',
                    ], true)) {
                        $sheet->getStyle('C'.$rowNumber.':D'.$rowNumber)
                            ->getFont()->setBold(true);
                    }

                    if ($sheet->getCell('A'.$rowNumber)->getValue() === ''
                        && $sheet->getCell('B'.$rowNumber)->getValue() === ''
                        && $sheet->getCell('C'.$rowNumber)->getValue() === ''
                        && is_numeric($sheet->getCell('D'.$rowNumber)->getValue())) {
                        $sheet->getStyle('D'.$rowNumber)
                            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    }

                    if ($sheet->getCell('A'.$rowNumber)->getValue() !== ''
                        && $sheet->getCell('A'.$rowNumber)->getValue() !== 'FECHA') {
                        $sheet->getStyle('A'.$rowNumber)->getFont()->setBold(true);
                    }
                }
            },
        ];
    }
}
