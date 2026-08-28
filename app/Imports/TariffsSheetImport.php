<?php

namespace App\Imports;

use App\Models\Service;
use App\Models\SubCategory;
use App\Models\Tariff;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;

class TariffsSheetImport implements SkipsEmptyRows, ToCollection
{
    public int $imported = 0;

    public int $skipped = 0;

    public array $errors = [];

    public function collection(Collection $rows): void
    {
        if ($rows->isEmpty()) {
            return;
        }

        $headers = $this->normalizeHeaders($rows->first()->toArray());
        foreach ($rows->skip(1) as $index => $row) {
            $values = array_pad(array_slice($row->toArray(), 0, count($headers)), count($headers), null);
            $data = array_combine($headers, $values);
            $supplierName = trim((string) ($data['proveedor'] ?? ''));
            $serviceName = trim((string) ($data['servicio'] ?? $data['nombre_servicio'] ?? ''));
            $subcategoryName = trim((string) ($data['subcategoria'] ?? $data['tipo'] ?? ''));

            if ($supplierName === '' || $serviceName === '' || $subcategoryName === '') {
                $this->skip($index, 'proveedor, servicio y subcategoria son obligatorios.');

                continue;
            }

            $service = Service::whereRaw('LOWER(name_service) = ?', [mb_strtolower($serviceName)])
                ->whereHas('supplier', fn ($query) => $query->whereRaw('LOWER(supplier_name) = ?', [mb_strtolower($supplierName)]))
                ->first();
            $subcategory = SubCategory::whereRaw('LOWER(name) = ?', [mb_strtolower($subcategoryName)])->first();

            if (! $service) {
                $this->skip($index, 'servicio no encontrado: '.$serviceName.'.');

                continue;
            }
            if (! $subcategory || $subcategory->id_category !== $service->id_category) {
                $this->skip($index, 'subcategoria no encontrada para el servicio: '.$subcategoryName.'.');

                continue;
            }

            $pricingType = trim((string) ($data['pricing_type'] ?? 'flat')) ?: 'flat';
            if (! in_array($pricingType, ['flat', 'tiered'], true)) {
                $this->skip($index, 'pricing_type debe ser flat o tiered.');

                continue;
            }

            $min = $this->nullableInteger($data['min_people_count'] ?? $data['minimo'] ?? null);
            $max = $this->nullableInteger($data['max_people_count'] ?? $data['maximo'] ?? null);
            $price = $data['price'] ?? $data['precio'] ?? null;
            if ($price === '' || $price === null || ! is_numeric($price)) {
                $this->skip($index, 'precio debe ser numérico.');

                continue;
            }

            Tariff::updateOrCreate(
                [
                    'id_service' => $service->id_service,
                    'id_subcategories' => $subcategory->id_subcategories,
                    'id_season' => null,
                    'pricing_type' => $pricingType,
                    'min_people_count' => $min,
                    'max_people_count' => $max,
                ],
                ['price' => (float) $price, 'status' => 'active']
            );
            $this->imported++;
        }
    }

    private function skip(int|string $index, string $message): void
    {
        $this->skipped++;
        $this->errors[] = 'Fila '.((int) $index + 2).': '.$message;
    }

    private function nullableInteger(mixed $value): ?int
    {
        return $value === '' || $value === null ? null : (int) $value;
    }

    private function normalizeHeaders(array $headers): array
    {
        return array_map(function ($header): string {
            $header = mb_strtolower(trim((string) $header));
            $header = str_replace(['á', 'é', 'í', 'ó', 'ú', 'ñ'], ['a', 'e', 'i', 'o', 'u', 'n'], $header);

            return preg_replace('/[^a-z0-9]+/', '_', $header) ?? '';
        }, $headers);
    }
}
