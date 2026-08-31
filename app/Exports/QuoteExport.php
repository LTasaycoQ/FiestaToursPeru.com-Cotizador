<?php

namespace App\Exports;

use App\Models\Quote;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class QuoteExport implements FromCollection, ShouldAutoSize, WithHeadings, WithStyles, WithTitle
{
    public function __construct(private readonly int $quoteId) {}

    public function title(): string
    {
        return 'Servicios';
    }

    public function headings(): array
    {
        return ['DÍA', 'SERVICIO', 'PAX', 'PRECIO UNITARIO', 'SUBTOTAL'];
    }

    public function collection(): Collection
    {
        $quote = Quote::with([
            'quoteDays.details.service',
            'quoteDays.details.tariff',
        ])->findOrFail($this->quoteId);

        $rows = collect();
        $grandSubtotal = 0;
        $passengers = (int) ($quote->passengers_count ?: 1);

        foreach ($quote->quoteDays->sortBy('day_number') as $day) {
            foreach ($day->details->sortBy('id_detail_quote') as $detail) {
                $serviceName = $detail->service?->name_service ?? 'Servicio eliminado';
                $unitPrice = (float) ($detail->unit_price ?? 0);
                $quantity = (int) ($detail->quantity ?: $passengers);
                $subtotal = (float) ($detail->subtotal ?? ($unitPrice * $quantity));

                $rows->push([
                    'Día '.$day->day_number,
                    $serviceName,
                    $quantity,
                    $unitPrice,
                    $subtotal,
                ]);

                $grandSubtotal += $subtotal;
            }
        }

        $rows->push(['', '', '', 'TOTAL GENERAL', $grandSubtotal]);
        $rows->push(['', '', '', 'TOTAL X PASAJEROS', $grandSubtotal * $passengers]);

        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '7DC7C7']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ],
            'A2:E'.($sheet->getHighestRow()) => [
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
            ],
        ];
    }
}
