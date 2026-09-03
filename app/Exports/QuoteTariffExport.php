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
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class QuoteTariffExport implements FromCollection, WithEvents, WithHeadings, WithStyles, WithTitle
{
    private const PRIVATE_RANGES = ['1', '2', '3/4', '5/9', '10/14', '15/19', '20/24', '25/29', '30/up'];

    public function __construct(private readonly int $quoteId) {}

    public function title(): string
    {
        return 'Tarifas';
    }

    public function headings(): array
    {
        return [
            ['Traslados Tours y Paquetes', 'Regular Económico', '', 'Regular VIP', '', 'Servicios Privados', '', '', '', '', '', '', '', ''],
            ['', 'Min 1', 'Min 2', 'Min 1', 'Min 2', ...self::PRIVATE_RANGES],
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FFFF99']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ],
            2 => [
                'font' => ['bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FFFF99']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event): void {
                $sheet = $event->sheet->getDelegate();
                $sheet->mergeCells('A1:A2');
                $sheet->mergeCells('B1:C1');
                $sheet->mergeCells('D1:E1');
                $sheet->mergeCells('F1:N1');
                $lastRow = $sheet->getHighestRow();
                $sheet->getStyle('A1:N2')
                    ->getBorders()->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN)
                    ->getColor()->setRGB('000000');
                $sheet->getStyle('A1:A'.$lastRow)
                    ->getBorders()->getLeft()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('N1:N'.$lastRow)
                    ->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('A'.$lastRow.':N'.$lastRow)
                    ->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('A1:N'.$sheet->getHighestRow())
                    ->getFont()
                    ->setName('Arial')
                    ->setSize(9);
                $sheet->getStyle('A1:N'.$sheet->getHighestRow())
                    ->getAlignment()
                    ->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle('B3:N'.$lastRow)
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                // Ajusta aquí el ancho de cada columna del tarifario.
                $sheet->getColumnDimension('A')->setWidth(58); // Nombre del servicio / día.
                $tariffColumnWidths = [
                    'B' => 6, // Regular Económico - Min 1.
                    'C' => 6, // Regular Económico - Min 2.
                    'D' => 6, // Regular VIP - Min 1.
                    'E' => 6, // Regular VIP - Min 2.
                    'F' => 6, // Privado - 1.
                    'G' => 6, // Privado - 2.
                    'H' => 6, // Privado - 3/4.
                    'I' => 6, // Privado - 5/9.
                    'J' => 6, // Privado - 10/14.
                    'K' => 6, // Privado - 15/19.
                    'L' => 6, // Privado - 20/24.
                    'M' => 6, // Privado - 25/29.
                    'N' => 6, // Privado - 30/up.
                ];
                foreach ($tariffColumnWidths as $column => $width) {
                    $sheet->getColumnDimension($column)->setWidth($width);
                }
                $sheet->getStyle('A1:N'.$sheet->getHighestRow())
                    ->getAlignment()->setWrapText(true);

                for ($rowNumber = 3; $rowNumber <= $sheet->getHighestRow(); $rowNumber++) {
                    $firstCell = (string) $sheet->getCell('A'.$rowNumber)->getValue();
                    if ($firstCell !== '') {
                        $sheet->getStyle('A'.$rowNumber)
                            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    }

                    if (str_starts_with($firstCell, 'Día ')) {
                        $sheet->getStyle('A'.$rowNumber.':N'.$rowNumber)
                            ->getFont()->setBold(true);
                    }

                    if ($sheet->getCell('A'.$rowNumber)->getValue() === 'Total servicios') {
                        $sheet->getStyle('A'.$rowNumber.':N'.$rowNumber)
                            ->getFont()->setBold(true);
                        $sheet->getStyle('B'.$rowNumber.':N'.$rowNumber)
                            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    }
                }
            },
        ];
    }

    public function collection(): Collection
    {
        $quote = Quote::with([
            'quoteDays.details.service.tariffs.subCategory',
        ])->findOrFail($this->quoteId);

        $rows = collect();
        $totals = array_fill(0, 13, 0);

        foreach ($quote->quoteDays->sortBy('day_number')->values() as $dayIndex => $day) {
            if ($dayIndex > 0) {
                $rows->push(array_fill(0, 14, ''));
            }

            $rows->push([
                'Día '.$day->day_number.' - '.$day->name,
                ...array_fill(0, 13, ''),
            ]);

            foreach ($day->details->unique('id_service') as $detail) {
                $serviceRow = $this->serviceRow(
                    $detail->service?->name_service ?? 'Servicio eliminado',
                    $detail->service?->tariffs?->where('status', 'active') ?? collect(),
                );
                $rows->push($serviceRow);

                foreach (array_slice($serviceRow, 1) as $index => $price) {
                    if (is_numeric($price)) {
                        $totals[$index] += (float) $price;
                    }
                }
            }
        }

        $rows->push(['Total servicios', ...$totals]);

        return $rows;
    }

    private function serviceRow(string $serviceName, Collection $tariffs): array
    {
        $prices = array_fill(0, 13, '');
        foreach ($tariffs as $tariff) {
            $subcategory = mb_strtolower($tariff->subCategory?->name ?? '');
            $price = (float) $tariff->price;
            if (str_contains($subcategory, 'econom')) {
                $prices[$tariff->min_people_count === 2 ? 1 : 0] = $price;
            } elseif (str_contains($subcategory, 'vip')) {
                $prices[$tariff->min_people_count === 2 ? 3 : 2] = $price;
            } elseif (str_contains($subcategory, 'priv')) {
                foreach ($this->privateRangeIndexes($tariff->min_people_count, $tariff->max_people_count) as $index) {
                    $prices[4 + $index] = $price;
                }
            }
        }

        return [$serviceName, ...$prices];
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
