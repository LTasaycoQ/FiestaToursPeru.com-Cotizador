<?php

namespace App\Imports;

use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\SubCategory;
use App\Models\Supplier;
use App\Models\Tariff;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;

class ServicesImport implements SkipsEmptyRows, ToCollection
{
    private const PRIVATE_RANGES = [
        '1' => [1, 1],
        '2' => [2, 2],
        '3/4' => [3, 4],
        '5/9' => [5, 9],
        '10/14' => [10, 14],
        '15/19' => [15, 19],
        '20/24' => [20, 24],
        '25/29' => [25, 29],
        '30/up' => [30, null],
    ];

    public int $imported = 0;

    public int $tariffsImported = 0;

    public int $skipped = 0;

    public array $errors = [];

    public function __construct(private readonly int $marketId) {}

    public function collection(Collection $rows): void
    {
        if ($rows->count() < 2) {
            $this->errors[] = 'El archivo no contiene una cabecera y actividades.';

            return;
        }

        $header = $rows->get(0)->toArray();
        $subheader = $rows->get(1)->toArray();
        $serviceColumn = $this->findColumn($header, 'traslados tours y paquetes');
        if ($serviceColumn === null) {
            $this->errors[] = 'No se encontró la columna Traslados Tours y Paquetes.';

            return;
        }

        $columns = $this->tariffColumns($header, $subheader);
        foreach ($rows->slice(2) as $index => $row) {
            $values = $row->toArray();
            $supplierName = trim((string) ($values[$this->findColumn($header, 'proveedor') ?? -1] ?? ''));
            $categoryName = trim((string) ($values[$this->findColumn($header, 'categoria') ?? -1] ?? ''));
            $serviceName = trim((string) ($values[$serviceColumn] ?? ''));

            if ($supplierName === '' || $categoryName === '' || $serviceName === '') {
                continue;
            }

            $supplier = Supplier::whereRaw('LOWER(supplier_name) = ?', [mb_strtolower($supplierName)])
                ->whereNull('deleted_at')
                ->first();
            $category = ServiceCategory::whereRaw('LOWER(name) = ?', [mb_strtolower($categoryName)])->first();
            if (! $supplier || ! $category) {
                $this->skipped++;
                $this->errors[] = 'Fila '.((int) $index + 3).': proveedor o categoría no encontrados.';

                continue;
            }

            $service = Service::updateOrCreate(
                ['id_supplier' => $supplier->id_supplier, 'name_service' => $serviceName],
                [
                    'id_category' => $category->id_category,
                    'id_labels' => $this->marketId,
                    'pricing_type' => 'tiered',
                    'status' => 'active',
                ]
            );
            $this->imported++;

            foreach ($columns as $column) {
                $price = $values[$column['index']] ?? null;
                if ($price === '' || $price === null || ! is_numeric($price)) {
                    continue;
                }

                $subcategory = SubCategory::where('id_category', $category->id_category)
                    ->whereRaw('LOWER(name) = ?', [mb_strtolower($column['subcategory'])])
                    ->first();
                if (! $subcategory) {
                    continue;
                }

                Tariff::updateOrCreate(
                    [
                        'id_service' => $service->id_service,
                        'id_subcategories' => $subcategory->id_subcategories,
                        'id_season' => null,
                        'min_people_count' => $column['min'],
                        'max_people_count' => $column['max'],
                    ],
                    ['pricing_type' => 'tiered', 'price' => (float) $price, 'status' => 'active']
                );
                $this->tariffsImported++;
            }
        }
    }

    private function tariffColumns(array $header, array $subheader): array
    {
        $columns = [];
        $currentGroup = '';
        foreach ($subheader as $index => $value) {
            $range = trim((string) $value);
            $group = mb_strtolower(trim((string) ($header[$index] ?? '')));
            if ($group !== '') {
                $currentGroup = $group;
            }
            if ($range === '' || $currentGroup === '') {
                continue;
            }

            if (str_contains($currentGroup, 'econom')) {
                $columns[] = ['index' => $index, 'subcategory' => 'Regular Economico', 'min' => $range === 'Min 2' ? 2 : 1, 'max' => $range === 'Min 2' ? 2 : 1];
            } elseif (str_contains($currentGroup, 'vip')) {
                $columns[] = ['index' => $index, 'subcategory' => 'Regular Vip', 'min' => $range === 'Min 2' ? 2 : 1, 'max' => $range === 'Min 2' ? 2 : 1];
            } elseif (str_contains($currentGroup, 'priv')) {
                $bounds = self::PRIVATE_RANGES[$range] ?? null;
                if ($bounds) {
                    $columns[] = ['index' => $index, 'subcategory' => 'Privado', 'min' => $bounds[0], 'max' => $bounds[1]];
                }
            }
        }

        return $columns;
    }

    private function findColumn(array $headers, string $needle): ?int
    {
        foreach ($headers as $index => $header) {
            $value = mb_strtolower(trim((string) $header));
            $value = str_replace(['á', 'é', 'í', 'ó', 'ú', 'ñ'], ['a', 'e', 'i', 'o', 'u', 'n'], $value);
            if (str_contains($value, $needle)) {
                return (int) $index;
            }
        }

        return null;
    }
}
