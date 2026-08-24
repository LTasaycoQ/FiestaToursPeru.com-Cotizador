<?php

namespace App\Imports;

use App\Models\Client;
use App\Models\Country;
use App\Models\City;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class ClientsImport implements ToCollection, SkipsEmptyRows
{
    public int $imported = 0;
    public int $skipped  = 0;
    public array $errors = [];

    private const MAX_PHONE   = 20;
    private const MAX_ADDRESS = 255;

    // Palabras clave que identifican la fila de encabezados reales
    private const HEADER_MARKERS = ['NOMBRE', 'AGENCIA_CLIENTE'];

    // Si el primer campo de una fila contiene alguno de estos textos,
    // se considera fin de los datos (footer / totales del export bonito)
    private const STOP_MARKERS = ['TOTAL DE REGISTROS', 'FIESTA TOURS PERU'];

    public function collection(Collection $rows)
    {
        $headerRowIndex = $this->findHeaderRowIndex($rows);

        if ($headerRowIndex === null) {
            $this->errors[] = 'No se encontró la fila de encabezados (NOMBRE, PAIS, Contacto, Mail, Telefono...). Verifica el archivo.';
            return;
        }

        $headerKeys = $this->normalizeHeaders($rows[$headerRowIndex]->toArray());

        $grouped = [];
        $currentAgencia   = null;
        $currentPais      = null;
        $currentCiudad    = null;
        $currentTelefono  = null;
        $currentDireccion = null;

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

            $nombreRaw    = trim((string) ($assoc['nombre'] ?? $assoc['agencia_cliente'] ?? ''));
            $pais         = trim((string) ($assoc['pais'] ?? $assoc['country_name'] ?? ''));
            $ciudad       = trim((string) ($assoc['ciudad'] ?? $assoc['city_name'] ?? ''));
            $contacto     = trim((string) ($assoc['contacto'] ?? $assoc['contacto_1'] ?? ''));
            $mail         = trim((string) ($assoc['mail'] ?? $assoc['email_1'] ?? ''));
            $telefonoRaw  = trim((string) ($assoc['telefono'] ?? $assoc['telefono_1'] ?? ''));
            $direccionRaw = trim((string) ($assoc['direccion'] ?? $assoc['address'] ?? ''));

            // Detectar footer/totales del archivo exportado y detener el procesamiento
            $firstCellUpper = mb_strtoupper($nombreRaw);
            foreach (self::STOP_MARKERS as $marker) {
                if ($firstCellUpper !== '' && str_contains($firstCellUpper, $marker)) {
                    break 2;
                }
            }

            if ($nombreRaw !== '') {
                $currentAgencia   = $nombreRaw;
                $currentPais      = $pais   !== '' ? $pais   : $currentPais;
                $currentCiudad    = $ciudad !== '' ? $ciudad : $currentCiudad;
                $currentTelefono  = $telefonoRaw !== '' ? $telefonoRaw : null;
                $currentDireccion = $direccionRaw !== '' ? $direccionRaw : null;
            }

            if (empty($currentAgencia)) {
                $this->skipped++;
                continue;
            }

            $grouped[$currentAgencia] ??= [
                'pais'      => $currentPais,
                'ciudad'    => $currentCiudad,
                'telefono'  => $currentTelefono,
                'direccion' => $currentDireccion,
                'contactos' => [],
            ];

            if ($contacto !== '') {
                $grouped[$currentAgencia]['contactos'][] = [
                    'nombre'   => $contacto,
                    'email'    => $mail !== '' ? $mail : null,
                    'telefono' => $currentTelefono,
                ];
            }
        }

        foreach ($grouped as $agencia => $data) {
            try {
                $address   = $this->safeTruncate($data['direccion'] ?? null, self::MAX_ADDRESS);
                $countryId = $this->resolveCountryId($data['pais'] ?? null);
                $cityId    = $this->resolveCityId($data['ciudad'] ?? null, $countryId);

                $client = Client::firstOrCreate(
                    ['name_client' => $agencia],
                    [
                        'business_name' => null,
                        'tax_code'      => null,
                        'general_phone' => $this->firstPhone($data['telefono']),
                        'general_email' => $data['contactos'][0]['email'] ?? null,
                        'id_countries'  => $countryId,
                        'id_cities'     => $cityId,
                        'address'       => $address,
                    ]
                );

                $clientUpdates = [];
                if (empty($client->id_countries) && $countryId) {
                    $clientUpdates['id_countries'] = $countryId;
                }
                if (empty($client->id_cities) && $cityId) {
                    $clientUpdates['id_cities'] = $cityId;
                }
                if (empty($client->general_phone) && !empty($data['telefono'])) {
                    $clientUpdates['general_phone'] = $this->firstPhone($data['telefono']);
                }
                if (empty($client->general_email) && !empty($data['contactos'][0]['email'] ?? null)) {
                    $clientUpdates['general_email'] = $data['contactos'][0]['email'];
                }
                if (empty($client->address) && !empty($address)) {
                    $clientUpdates['address'] = $address;
                }
                if (!empty($clientUpdates)) {
                    $client->update($clientUpdates);
                }

                foreach ($data['contactos'] as $i => $contactoData) {
                    $email = $contactoData['email'];

                    if ($email && $client->contacts()->where('email', $email)->exists()) {
                        $this->skipped++;
                        continue;
                    }

                    $tel1 = null;
                    $tel2 = null;
                    if ($i === 0 && !empty($contactoData['telefono'])) {
                        [$tel1, $tel2] = $this->splitPhones($contactoData['telefono']);
                    }

                    $esPrincipal = ($i === 0) && $client->contacts()->count() === 0;

                    $client->contacts()->create([
                        'name'          => $contactoData['nombre'],
                        'last_names'    => null,
                        'qualification' => null,
                        'email'         => $email,
                        'first_phone'   => $tel1,
                        'second_phone'  => $tel2,
                        'es_principal'  => $esPrincipal,
                    ]);
                }

                $this->imported++;

            } catch (\Exception $e) {
                $this->errors[] = "Error en '{$agencia}': " . $e->getMessage();
            }
        }
    }

    /**
     * Busca dentro de las primeras filas del archivo cuál es la fila real
     * de encabezados (contiene "NOMBRE" o similar), ignorando banners/títulos.
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
     * igual a como lo hacía WithHeadingRow (nombre, pais, contacto, mail, telefono...).
     */
    private function normalizeHeaders(array $headers): array
    {
        return array_map(function ($h) {
            $h = mb_strtolower(trim((string) $h));
            $h = preg_replace('/[^a-z0-9]+/', '_', $h);
            return trim($h, '_') ?: 'col';
        }, $headers);
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
