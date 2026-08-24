<?php

namespace App\Imports;

use App\Models\Supplier;
use App\Models\CategorySupplier;
use App\Models\Contact;
use App\Models\Country;
use App\Models\City;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class SuppliersImport implements ToCollection, SkipsEmptyRows
{
    public int $imported = 0;
    public int $skipped  = 0;
    public array $errors = [];

    private const MAX_SUPPLIER_NAME = 100;
    private const MAX_BUSINESS_NAME = 150;
    private const MAX_TAX_CODE      = 20;
    private const MAX_PHONE         = 20;
    private const MAX_EMAIL         = 120;
    private const MAX_ADDRESS       = 255;
    private const MAX_CATEGORY      = 100;

    // Palabras clave que identifican la fila de encabezados reales
    private const HEADER_MARKERS = ['PROVEEDOR', 'NOMBRE_PROVEEDOR', 'RAZON_SOCIAL', 'NOMBRE', 'RAZÓN SOCIAL'];

    // Si el primer campo de una fila contiene alguno de estos textos,
    // se considera fin de los datos (footer / totales del export bonito)
    private const STOP_MARKERS = ['TOTAL DE REGISTROS', 'FIESTA TOURS PERU'];

    public function collection(Collection $rows)
    {
        $headerRowIndex = $this->findHeaderRowIndex($rows);

        if ($headerRowIndex === null) {
            $this->errors[] = 'No se encontró la fila de encabezados (PROVEEDOR, PAIS, Contacto, Mail, Telefono...). Verifica el archivo.';
            return;
        }

        $headerKeys = $this->normalizeHeaders($rows[$headerRowIndex]->toArray());

        $grouped = [];
        $currentProveedor    = null;
        $currentRazonSocial  = null;
        $currentRuc          = null;
        $currentPais         = null;
        $currentCiudad       = null;
        $currentTelefono     = null;
        $currentEmailGeneral = null;
        $currentDireccion    = null;
        $currentCategoria    = null;

        foreach ($rows->slice($headerRowIndex + 1) as $row) {
            $rowArr = $row->toArray();

            // Si la fila viene "vacía" del todo, se salta (SkipsEmptyRows ya ayuda, pero por si acaso)
            if (collect($rowArr)->filter(fn($v) => trim((string) $v) !== '')->isEmpty()) {
                continue;
            }

            $assoc = array_combine(
                $headerKeys,
                array_pad(array_slice($rowArr, 0, count($headerKeys)), count($headerKeys), null)
            );

            // --- Nombre del proveedor (NUNCA debe caer en razon_social) ---
            $nombreRaw       = trim((string) ($assoc['proveedor'] ?? $assoc['nombre_proveedor'] ?? $assoc['nombre'] ?? ''));
            $razonSocialRaw  = trim((string) ($assoc['razon_social'] ?? $assoc['business_name'] ?? ''));
            $rucRaw          = trim((string) ($assoc['ruc'] ?? $assoc['tax_code'] ?? $assoc['nit'] ?? ''));
            $pais            = trim((string) ($assoc['pais'] ?? $assoc['country_name'] ?? ''));
            $ciudad          = trim((string) ($assoc['ciudad'] ?? $assoc['city_name'] ?? ''));
            $categoria       = trim((string) ($assoc['categoria'] ?? $assoc['category_name'] ?? $assoc['rubro'] ?? ''));

            // Email GENERAL del proveedor (columna "Email")
            $emailGeneralRaw = trim((string) ($assoc['email'] ?? $assoc['mail'] ?? $assoc['general_email'] ?? ''));

            // Teléfono GENERAL del proveedor (columna "Teléfono")
            $telefonoRaw     = trim((string) ($assoc['telefono'] ?? $assoc['telefono_1'] ?? ''));

            $direccionRaw    = trim((string) ($assoc['direccion'] ?? $assoc['address'] ?? ''));

            // --- Datos del contacto (columnas "Contacto Principal", "Email Contacto", "Teléfono Contacto") ---
            $contacto        = trim((string) ($assoc['contacto_principal'] ?? $assoc['contacto'] ?? $assoc['contacto_1'] ?? ''));
            $mailContacto    = trim((string) ($assoc['email_contacto'] ?? $assoc['email_1'] ?? $assoc['mail_contacto'] ?? $assoc['mail'] ?? ''));
            $telefonoContactoRaw = trim((string) ($assoc['telefono_contacto'] ?? $assoc['telefono_1'] ?? ''));

            // Detectar footer/totales del archivo exportado y detener el procesamiento
            $firstCellUpper = mb_strtoupper($nombreRaw);
            foreach (self::STOP_MARKERS as $marker) {
                if ($firstCellUpper !== '' && str_contains($firstCellUpper, $marker)) {
                    break 2;
                }
            }

            if ($nombreRaw !== '') {
                $currentProveedor    = $nombreRaw;
                $currentRazonSocial  = $razonSocialRaw !== '' ? $razonSocialRaw : null;
                $currentRuc          = $rucRaw !== '' ? $rucRaw : null;
                $currentPais         = $pais   !== '' ? $pais   : $currentPais;
                $currentCiudad       = $ciudad !== '' ? $ciudad : $currentCiudad;
                $currentTelefono     = $telefonoRaw !== '' ? $telefonoRaw : null;
                $currentEmailGeneral = $emailGeneralRaw !== '' ? $emailGeneralRaw : null;
                $currentDireccion    = $direccionRaw !== '' ? $direccionRaw : null;
                $currentCategoria    = $categoria !== '' ? $categoria : null;
            }

            if (empty($currentProveedor)) {
                $this->skipped++;
                continue;
            }

            $grouped[$currentProveedor] ??= [
                'business_name' => $currentRazonSocial,
                'tax_code'      => $currentRuc,
                'pais'          => $currentPais,
                'ciudad'        => $currentCiudad,
                'telefono'      => $currentTelefono,
                'email_general' => $currentEmailGeneral,
                'direccion'     => $currentDireccion,
                'categoria'     => $currentCategoria,
                'contactos'     => [],
            ];

            if ($contacto !== '') {
                // Prioridad para el teléfono del contacto:
                // 1) columna dedicada "Teléfono Contacto" (plantilla nueva)
                // 2) el teléfono de ESTA MISMA fila (formato viejo: cada contacto
                //    adicional trae su propio teléfono en la columna "Telefono")
                // 3) el teléfono general persistido del proveedor, como último respaldo
                if ($telefonoContactoRaw !== '') {
                    $telefonoParaContacto = $telefonoContactoRaw;
                } elseif ($telefonoRaw !== '') {
                    $telefonoParaContacto = $telefonoRaw;
                } else {
                    $telefonoParaContacto = $currentTelefono;
                }

                $grouped[$currentProveedor]['contactos'][] = [
                    'nombre'   => $contacto,
                    'email'    => $mailContacto !== '' ? $mailContacto : null,
                    'telefono' => $telefonoParaContacto,
                ];
            }
        }

        foreach ($grouped as $proveedor => $data) {
            try {
                $address    = $this->safeTruncate($data['direccion'] ?? null, self::MAX_ADDRESS);
                $countryId  = $this->resolveCountryId($data['pais'] ?? null);
                $cityId     = $this->resolveCityId($data['ciudad'] ?? null, $countryId);
                $categoryId = $this->resolveCategoryId($data['categoria'] ?? null);

                // Email general del proveedor: prioriza la columna "Email";
                // si no vino, usa el email del primer contacto como respaldo.
                $generalEmail = $data['email_general'] ?? ($data['contactos'][0]['email'] ?? null);

                $supplier = Supplier::firstOrCreate(
                    ['supplier_name' => $this->safeTruncate($proveedor, self::MAX_SUPPLIER_NAME)],
                    [
                        'business_name'           => $this->safeTruncate($data['business_name'] ?? null, self::MAX_BUSINESS_NAME),
                        'tax_code'                => $this->safeTruncate($data['tax_code'] ?? null, self::MAX_TAX_CODE),
                        'general_phone'           => $this->firstPhone($data['telefono']),
                        'general_email'           => $this->safeTruncate($generalEmail, self::MAX_EMAIL),
                        'id_countries'            => $countryId,
                        'id_cities'               => $cityId,
                        'address'                 => $address,
                        'id_categories_suppliers' => $categoryId,
                    ]
                );

                $supplierUpdates = [];
                if (empty($supplier->id_countries) && $countryId) {
                    $supplierUpdates['id_countries'] = $countryId;
                }
                if (empty($supplier->id_cities) && $cityId) {
                    $supplierUpdates['id_cities'] = $cityId;
                }
                if (empty($supplier->id_categories_suppliers) && $categoryId) {
                    $supplierUpdates['id_categories_suppliers'] = $categoryId;
                }
                if (empty($supplier->general_phone) && !empty($data['telefono'])) {
                    $supplierUpdates['general_phone'] = $this->firstPhone($data['telefono']);
                }
                if (empty($supplier->general_email) && !empty($generalEmail)) {
                    $supplierUpdates['general_email'] = $this->safeTruncate($generalEmail, self::MAX_EMAIL);
                }
                if (empty($supplier->address) && !empty($address)) {
                    $supplierUpdates['address'] = $address;
                }
                if (empty($supplier->business_name) && !empty($data['business_name'])) {
                    $supplierUpdates['business_name'] = $this->safeTruncate($data['business_name'], self::MAX_BUSINESS_NAME);
                }
                if (empty($supplier->tax_code) && !empty($data['tax_code'])) {
                    $supplierUpdates['tax_code'] = $this->safeTruncate($data['tax_code'], self::MAX_TAX_CODE);
                }
                if (!empty($supplierUpdates)) {
                    $supplier->update($supplierUpdates);
                }

                foreach ($data['contactos'] as $i => $contactoData) {
                    $email = $this->safeTruncate($contactoData['email'], self::MAX_EMAIL);

                    if ($email && $supplier->contacts()->where('email', $email)->exists()) {
                        $this->skipped++;
                        continue;
                    }

                    $tel1 = null;
                    $tel2 = null;
                    if (!empty($contactoData['telefono'])) {
                        [$tel1, $tel2] = $this->splitPhones($contactoData['telefono']);
                    }

                    $esPrincipal = ($i === 0) && $supplier->contacts()->count() === 0;

                    $supplier->contacts()->create([
                        'id_client'      => null,
                        'name'           => $contactoData['nombre'],
                        'last_names'     => null,
                        'qualification'  => null,
                        'email'          => $email,
                        'first_phone'    => $tel1,
                        'second_phone'   => $tel2,
                        'es_principal'   => $esPrincipal,
                        'Date_of_birth'  => null,
                    ]);
                }

                $this->imported++;

            } catch (\Exception $e) {
                $this->errors[] = "Error en '{$proveedor}': " . $e->getMessage();
            }
        }
    }

    /**
     * Busca dentro de las primeras filas del archivo cuál es la fila real
     * de encabezados (contiene "PROVEEDOR" o similar), ignorando banners/títulos.
     */
    private function findHeaderRowIndex(Collection $rows): ?int
    {
        $limit = min($rows->count(), 15); // solo revisamos las primeras 15 filas

        for ($i = 0; $i < $limit; $i++) {
            $values = collect($rows[$i]->toArray())
                ->map(fn($v) => mb_strtoupper(trim((string) $v)))
                ->toArray();

            foreach (self::HEADER_MARKERS as $marker) {
                if (in_array($marker, $values, true)) {
                    return $i;
                }
            }
        }

        return null;
    }

    /**
     * Convierte los valores de la fila de encabezados a claves tipo snake_case,
     * igual a como lo hacía WithHeadingRow (proveedor, pais, contacto, mail, telefono...).
     * Además normaliza tildes/acentos para que "Razón Social" -> "razon_social",
     * "País" -> "pais", "Teléfono" -> "telefono", etc.
     */
    private function normalizeHeaders(array $headers): array
    {
        return array_map(function ($h) {
            $h = mb_strtolower(trim((string) $h));
            $h = $this->stripAccents($h);
            $h = preg_replace('/[^a-z0-9]+/', '_', $h);
            return trim($h, '_') ?: 'col';
        }, $headers);
    }

    /**
     * Quita tildes/diacríticos comunes en español para que la normalización
     * de encabezados sea consistente sin importar cómo vengan escritos
     * (Ó, í, é, ñ, etc.).
     */
    private function stripAccents(string $value): string
    {
        $replacements = [
            'á' => 'a', 'à' => 'a', 'ä' => 'a', 'â' => 'a',
            'é' => 'e', 'è' => 'e', 'ë' => 'e', 'ê' => 'e',
            'í' => 'i', 'ì' => 'i', 'ï' => 'i', 'î' => 'i',
            'ó' => 'o', 'ò' => 'o', 'ö' => 'o', 'ô' => 'o',
            'ú' => 'u', 'ù' => 'u', 'ü' => 'u', 'û' => 'u',
            'ñ' => 'n',
        ];

        return strtr($value, $replacements);
    }

    private function resolveCountryId(?string $name): ?int
    {
        $name = trim((string) $name);
        if ($name === '') {
            return null;
        }

        $country = Country::whereRaw('LOWER(name) = ?', [mb_strtolower($name)])->first();

        if (!$country) {
            $country = Country::create(['name' => $name]);
        }

        return $country->id_countries;
    }

    private function resolveCityId(?string $name, ?int $countryId): ?int
    {
        $name = trim((string) $name);
        if ($name === '' || !$countryId) {
            return null;
        }

        $city = City::whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
            ->where('country_id', $countryId)
            ->first();

        if (!$city) {
            $city = City::create([
                'country_id' => $countryId,
                'name'       => $name,
            ]);
        }

        return $city->id_cities;
    }

    private function resolveCategoryId(?string $name): ?int
    {
        $name = trim((string) $name);
        if ($name === '') {
            return null;
        }

        $category = CategorySupplier::whereRaw('LOWER(category_name) = ?', [mb_strtolower($name)])->first();

        if (!$category) {
            $category = CategorySupplier::create([
                'category_name' => $this->safeTruncate($name, self::MAX_CATEGORY),
            ]);
        }

        return $category->id_categories_suppliers;
    }

    private function splitPhones(?string $telefono): array
    {
        if (empty($telefono)) {
            return [null, null];
        }

        $telefono = str_replace("\xc2\xa0", ' ', $telefono);
        $telefono = trim($telefono);

        if (str_contains($telefono, '/')) {
            $parts = array_map('trim', explode('/', $telefono, 2));
            $tel1 = $parts[0] ?: null;
            $tel2 = $parts[1] ?: null;
        } else {
            $tel1 = $telefono;
            $tel2 = null;
        }

        return [
            $this->safeTruncate($tel1, self::MAX_PHONE),
            $this->safeTruncate($tel2, self::MAX_PHONE),
        ];
    }

    private function firstPhone(?string $telefono): ?string
    {
        [$tel1, ] = $this->splitPhones($telefono);
        return $tel1;
    }

    private function safeTruncate(?string $value, int $maxLength): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value);

        if ($value === '') {
            return null;
        }

        return mb_substr($value, 0, $maxLength);
    }
}
