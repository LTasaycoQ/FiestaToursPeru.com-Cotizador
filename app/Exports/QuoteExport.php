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
        if ($this->mode === 'tariff') {
            return [
                ['TIPO', 'DÍA', 'Traslados Tours y Paquetes', 'Regular Económico', '', 'Regular VIP', '', 'Servicios Privados', '', '', '', '', '', '', '', ''],
                ['', '', '', 'Min 1', 'Min 2', 'Min 1', 'Min 2', ...self::PRIVATE_RANGES],
            ];
        }

        return [
            ['', '', '', ''],
            ['FIESTA TOURS PERU', '', '', ''],
            ['Av San Luis 2644 San Borja T: +51-1 225-1336', '', '', ''],

            ['Para:', $quote->client->name_client ?? 'No Definido', '', ''],
            ['REF:', $quote->name ?? 'No Definido', '', ''],
            ['FECHA:', now()->format('d/m/Y'), '', ''],
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
            'accommodations.service.supplier',
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

                $quantity = (int) ($detail->quantity ?: $passengers);
                $rows->push([
                    'Día '.$day->day_number,
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
            $rows->push(['', '', '', '']);
            $rows->push(['HOSPEDAJE - OPCIÓN '.$optionNumber, '', '', '', '', '']);
            $rows->push(['DÍA', 'HOTEL', 'TIPO DE HABITACIÓN', 'N° HABITACIONES', 'PRECIO UNITARIO', 'SUBTOTAL']);

            $optionSubtotal = 0;
            foreach ($accommodations as $accommodation) {
                $subtotal = (float) ($accommodation->subtotal ?? 0);
                $optionSubtotal += $subtotal;
                $rows->push([
                    $accommodation->quoteDay ? 'Día '.$accommodation->quoteDay->day_number : '',
                    $this->hotelCell($accommodation),
                    $accommodation->tariff?->subCategory?->name ?? ucfirst($accommodation->room_type ?? 'Sin tipo'),
                    (int) ($accommodation->room_count ?? 0),
                    (float) ($accommodation->unit_price ?? 0),
                    $subtotal,
                ]);
            }

            $rows->push(['', '', '', '', 'TOTAL OPCIÓN '.$optionNumber, $optionSubtotal]);
        }

        return $rows;
    }

    private function hotelCell(mixed $accommodation): RichText
    {
        $hotelName = $accommodation->service?->supplier?->supplier_name ?? 'Hotel no especificado';
        $serviceName = $accommodation->service?->name_service ?? 'Servicio eliminado';
        $richText = new RichText;
        $richText->createTextRun($hotelName)->getFont()->setBold(true)->setColor(new Color('FF1F4E78'));
        $richText->createTextRun("\n".$serviceName)->getFont()->setSize(9)->setColor(new Color('FF7F8C8D'));

        return $richText;
    }

    public function styles(Worksheet $sheet): array
    {
        if ($this->mode === 'tariff') {
            return [
                1 => [
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '7DC7C7']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ],
                2 => [
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FCE33A']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ],
            ];
        }

        return [
            1 => [
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '1F4E78']],
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            ],
            3 => [
                'font' => ['bold' => true, 'size' => 13],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '548235']],
                'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => 'FFFFFF']],
            ],
            4 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '70AD47']],
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
                    $value = (string) $sheet->getCell('A'.$row->getRowIndex())->getValue();
                    if (str_starts_with($value, 'HOSPEDAJE - OPCIÓN')) {
                        $sheet->getStyle('A'.$row->getRowIndex().':F'.$row->getRowIndex())->applyFromArray([
                            'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => 'FFFFFF']],
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'C55A11']],
                        ]);
                        $sheet->getStyle('A'.($row->getRowIndex() + 1).':F'.($row->getRowIndex() + 1))->applyFromArray([
                            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'ED7D31']],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                        ]);
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
            'Servicio',
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
