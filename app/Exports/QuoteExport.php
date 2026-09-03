<?php

namespace App\Exports;

use App\Models\Quote;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
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

class QuoteExport implements FromCollection, ShouldAutoSize, WithEvents, WithHeadings, WithStyles, WithTitle
{
    private const PRIVATE_RANGES = ['1', '2', '3/4', '5/9', '10/14', '15/19', '20/24', '25/29', '30/up'];

    public function __construct(
        private readonly int $quoteId,
        private readonly string $mode = 'detail',
    ) {}

    public function title(): string
    {
        return $this->mode === 'tariff' ? 'Tarifas' : 'Servicios';
    }

    public function headings(): array
    {
        $quote = Quote::with('client')->findOrFail($this->quoteId);

        if ($this->mode === 'tariff') {
            return [
                ['TIPO', 'DÍA', 'Traslados Tours y Paquetes', 'Regular Económico', '', 'Regular VIP', '', 'Servicios Privados', '', '', '', '', '', '', '', ''],
                ['', '', '', 'Min 1', 'Min 2', 'Min 1', 'Min 2', ...self::PRIVATE_RANGES],
            ];
        }

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

        if ($this->mode === 'tariff') {
            $rows = collect();
            $passengers = $quote->passengers_count ?: null;

            foreach ($quote->quoteDays->sortBy('day_number') as $day) {
                foreach ($day->details->unique('id_service') as $detail) {
                    $tariffs = $passengers
                        ? collect([$detail->tariff])->filter()
                        : ($detail->service?->tariffs?->where('status', 'active') ?? collect());

                    $rows->push($this->serviceRow(
                        $day->day_number,
                        $detail->service?->name_service ?? 'Servicio eliminado',
                        $tariffs,
                        $detail->unit_price,
                        $passengers,
                    ));
                }
            }

            foreach ($quote->accommodations->sortBy(fn ($accommodation) => $accommodation->quoteDay?->day_number) as $accommodation) {
                $rows->push([
                    'Hotel',
                    $accommodation->quoteDay ? 'Día '.$accommodation->quoteDay->day_number : '',
                    $accommodation->service?->name_service ?? 'Hotel eliminado',
                    $accommodation->tariff?->subCategory?->name ?? ucfirst($accommodation->room_type ?? 'Sin tipo'),
                    '',
                    '',
                    '',
                    (int) ($accommodation->room_count ?? 0),
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    (float) ($accommodation->subtotal ?? 0),
                ]);
            }

            return $rows;
        }

        $rows = collect();
        $grandSubtotal = 0;
        $passengers = (int) ($quote->passengers_count ?: 1);

        foreach ($quote->quoteDays->sortBy('day_number') as $day) {
            foreach ($day->details->sortBy('id_detail_quote') as $detail) {
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
            $roomCodes = $this->hotelRoomCodes($accommodations);
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
        $richText->createTextRun("\n".$serviceName)->getFont()->setSize(9)->setColor(new Color('7191A8'));

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
                if ($this->mode === 'tariff') {
                    $sheet->mergeCells('A1:A2');
                    $sheet->mergeCells('B1:B2');
                    $sheet->mergeCells('C1:C2');
                    $sheet->mergeCells('D1:E1');
                    $sheet->mergeCells('F1:G1');
                    $sheet->mergeCells('H1:P1');
                    $sheet->getRowDimension(1)->setRowHeight(28);
                    $sheet->getRowDimension(2)->setRowHeight(22);
                    $sheet->freezePane('D3');

                    return;
                }

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

                if ($this->mode !== 'tariff') {
                    $sheet->getColumnDimension('A')->setWidth(14);
                    $sheet->getColumnDimension('B')->setWidth(18);
                    $sheet->getColumnDimension('C')->setAutoSize(false);
                    $sheet->getColumnDimension('C')->setWidth(40);
                    $sheet->getColumnDimension('D')->setWidth(11);
                    $sheet->getColumnDimension('E')->setWidth(11);
                    $sheet->getColumnDimension('F')->setWidth(11);
                    $sheet->getColumnDimension('G')->setWidth(11);
                    $sheet->getColumnDimension('H')->setWidth(13);
                    $sheet->getColumnDimension('I')->setWidth(13);
                    $sheet->getColumnDimension('J')->setWidth(13);
                    $sheet->getColumnDimension('K')->setWidth(14);
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
                            $sheet->getStyle('A'.$rowNumber.':K'.$rowNumber)
                                ->getBorders()->getAllBorders()
                                ->setBorderStyle(Border::BORDER_THIN)
                                ->getColor()->setRGB('000000');
                            $sheet->getStyle('A'.$rowNumber.':K'.$rowNumber)
                                ->getAlignment()
                                ->setHorizontal(Alignment::HORIZONTAL_LEFT);

                            continue;
                        }

                        if (is_numeric($columnB)) {
                            $sheet->mergeCells('D'.$rowNumber.':G'.$rowNumber);
                            $sheet->getStyle('H'.$rowNumber.':K'.$rowNumber)
                                ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        } elseif ($columnC) {
                            $sheet->getStyle('D'.$rowNumber)
                                ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
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
                }
            },
        ];
    }

    private function serviceRow(
        int $dayNumber,
        string $serviceName,
        Collection $tariffs,
        mixed $fallbackPrice,
        ?int $passengers,
    ): array {
        $prices = array_fill(0, 13, '');

        foreach ($tariffs as $tariff) {
            $subcategory = mb_strtolower($tariff->subCategory?->name ?? '');
            $price = (float) $tariff->price;

            if (str_contains($subcategory, 'econom')) {
                $prices[$tariff->min_people_count === 2 ? 1 : 0] = $price;
            } elseif (str_contains($subcategory, 'vip')) {
                $prices[$tariff->min_people_count === 2 ? 3 : 2] = $price;
            } elseif (str_contains($subcategory, 'priv')) {
                $privateIndexes = $this->privateRangeIndexes($tariff->min_people_count, $tariff->max_people_count);
                foreach ($privateIndexes as $index) {
                    $prices[4 + $index] = $price;
                }
            }
        }

        if ($tariffs->isEmpty() && $passengers) {
            $prices[0] = (float) $fallbackPrice;
        }

        return [
            $date,
            'Día '.$dayNumber,
            $serviceName,
            ...$prices,
        ];
    }

    private function privateRangeIndexes(?int $min, ?int $max): array
    {
        if ($min === 1 && $max === 30) {
            return range(0, count(self::PRIVATE_RANGES) - 1);
        }

        return match (true) {
            $min === 1 => [0],
            $min === 2 => [1],
            $min >= 3 && $min <= 4 => [2],
            $min >= 5 && $min <= 9 => [3],
            $min >= 10 && $min <= 14 => [4],
            $min >= 15 && $min <= 19 => [5],
            $min >= 20 && $min <= 24 => [6],
            $min >= 25 && $min <= 29 => [7],
            $min >= 30 => [8],
            default => [],
        };
    }
}
