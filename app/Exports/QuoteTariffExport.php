<?php

namespace App\Exports;

use App\Models\Quote;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class QuoteTariffExport implements FromCollection, WithHeadings, WithTitle
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
            ['TIPO', 'DÍA', 'Traslados Tours y Paquetes', 'Regular Económico', '', 'Regular VIP', '', 'Servicios Privados', '', '', '', '', '', '', '', ''],
            ['', '', '', 'Min 1', 'Min 2', 'Min 1', 'Min 2', ...self::PRIVATE_RANGES],
        ];
    }

    public function collection(): Collection
    {
        $quote = Quote::with([
            'quoteDays.details.service.tariffs.subCategory',
            'accommodations.quoteDay',
            'accommodations.service',
            'accommodations.tariff.subCategory',
        ])->findOrFail($this->quoteId);

        $rows = collect();
        foreach ($quote->quoteDays->sortBy('day_number') as $day) {
            foreach ($day->details->unique('id_service') as $detail) {
                $rows->push($this->serviceRow(
                    $day->day_number,
                    $detail->service?->name_service ?? 'Servicio eliminado',
                    $detail->service?->tariffs?->where('status', 'active') ?? collect(),
                ));
            }
        }

        foreach ($quote->accommodations->sortBy(fn ($accommodation) => $accommodation->quoteDay?->day_number) as $accommodation) {
            $rows->push([
                'Hotel',
                $accommodation->quoteDay ? 'Día '.$accommodation->quoteDay->day_number : '',
                $accommodation->service?->name_service ?? 'Hotel eliminado',
                $accommodation->tariff?->subCategory?->name ?? ucfirst($accommodation->room_type ?? 'Sin tipo'),
                '', '', '',
                (int) ($accommodation->room_count ?? 0),
                '', '', '', '', '', '', '',
                (float) ($accommodation->subtotal ?? 0),
            ]);
        }

        return $rows;
    }

    private function serviceRow(int $dayNumber, string $serviceName, Collection $tariffs): array
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

        return ['Servicio', 'Día '.$dayNumber, $serviceName, ...$prices];
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
