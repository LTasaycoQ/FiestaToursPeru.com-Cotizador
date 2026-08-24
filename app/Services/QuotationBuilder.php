<?php

namespace App\Services;

use App\Models\PaxRange;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Service;
use App\Models\ServiceRate;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QuotationBuilder
{
    public function __construct(private Quotation $quotation)
    {
    }

    public function addService(array $data): QuotationItem
    {
        Log::info('=== addService INICIO ===');
        Log::info('Datos recibidos:', $data);

        $service = null;
        $rate = null;

        // ✅ Camino preferido: viene id_service_rate (así lo manda el AJAX del form)
        if (isset($data['id_service_rate'])) {
            Log::info('Usando id_service_rate:', ['id' => $data['id_service_rate']]);
            $rate = ServiceRate::with('service')->findOrFail($data['id_service_rate']);
            $service = $rate->service;
            Log::info('Servicio encontrado por rate:', ['id' => $service->id_service, 'code' => $service->code]);
        } else {
            // Fallback: resolver la tarifa a partir de id_service + id_price_type (+ pax/fecha)
            Log::info('Buscando por id_price_type');
            $service = Service::findOrFail($data['id_service']);
            Log::info('Servicio encontrado:', ['id' => $service->id_service, 'code' => $service->code, 'kind' => $service->service_kind]);

            $idPriceType = $data['id_price_type'] ?? null;
            Log::info('idPriceType:', ['value' => $idPriceType]);

            if (!$idPriceType) {
                throw new \InvalidArgumentException('id_price_type es requerido.');
            }

            $date     = $data['service_date'] ?? null;
            $paxCount = $data['pax_count'] ?? null;

            $idPaxRange = null;
            if (in_array($service->service_kind, ['tour', 'transfer'])) {
                Log::info('Servicio es tour/transfer');
                if (!$paxCount) {
                    throw new \InvalidArgumentException(
                        "El servicio '{$service->code}' es tipo {$service->service_kind} y requiere pax_count."
                    );
                }
                $paxRange = PaxRange::findForPaxCount($paxCount);
                if (!$paxRange) {
                    throw new \RuntimeException("No existe un tramo de pax que cubra {$paxCount} pasajeros.");
                }
                $idPaxRange = $paxRange->id_pax_range;
                Log::info('PaxRange encontrado:', ['id' => $idPaxRange, 'label' => $paxRange->label]);
            }

            $rateQuery = ServiceRate::where('id_service', $service->id_service)
                ->where('id_price_type', $idPriceType)
                ->where('is_active', true);

            $idPaxRange
                ? $rateQuery->where('id_pax_range', $idPaxRange)
                : $rateQuery->whereNull('id_pax_range');

            if ($date) {
                $rateQuery->validOn($date);
            }

            $rate = $rateQuery->first();
            Log::info('Tarifa encontrada:', $rate ? ['id' => $rate->id_service_rate, 'price' => $rate->price_per_person] : ['result' => 'NO ENCONTRADA']);

            if (!$rate) {
                throw new \RuntimeException(
                    "No se encontró una tarifa vigente para '{$service->code}' con esa modalidad/tramo/fecha."
                );
            }
        }

        // ✅ Continuar con la creación del item
        $date = $data['service_date'] ?? null;
        $paxCount = $data['pax_count'] ?? null;

        $unitPrice = $date ? $rate->priceForDate($date) : (float) $rate->price_per_person;

        if (isset($data['quantity'])) {
            $quantity = (int) $data['quantity'];
        } elseif (in_array($service->service_kind, ['tour', 'transfer'])) {
            $quantity = $paxCount ?? 1;
        } elseif (in_array($service->service_kind, ['hotel', 'cruise', 'package'])) {
            $quantity = 1;
        } else {
            $quantity = 1;
        }

        if (in_array($service->service_kind, ['hotel', 'cruise', 'package'])) {
            $effectivePax = $paxCount ?? $this->quotation->pax_count ?? 1;
            $subtotal = $unitPrice * $effectivePax * $quantity;
        } else {
            $subtotal = $unitPrice * $quantity;
        }

        $description = $data['description']
            ?? ($this->quotation->language === 'en' && $service->name_en ? $service->name_en : $service->name_es);

        Log::info('Creando item:', [
            'id_service_rate' => $rate->id_service_rate,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'subtotal' => $subtotal,
            'description' => $description
        ]);

        $item = $this->quotation->items()->create([
            'id_service_rate' => $rate->id_service_rate,
            'description'     => $description,
            'day_number'      => $data['day_number'] ?? null,
            'service_date'    => $date,
            'quantity'        => $quantity,
            'unit_price'      => $unitPrice,
            'subtotal'        => $subtotal,
            'sort_order'      => $data['sort_order'] ?? ((int) $this->quotation->items()->max('sort_order') + 1),
            'notes'           => $data['notes'] ?? null,
        ]);

        Log::info('Item creado:', ['id' => $item->id_quotation_item]);
        $item->update(['subtotal' => $item->calculateSubtotal()]);

        return $item;
    }

    public function addServices(array $servicesData): Collection
    {
        Log::info('=== addServices INICIO ===', ['count' => count($servicesData)]);

        return DB::transaction(function () use ($servicesData) {
            $items = collect();
            foreach ($servicesData as $index => $data) {
                Log::info('Procesando servicio ' . $index . ':', $data);
                $items->push($this->addService($data));
            }

            $this->quotation->recalculateTotals();
            Log::info('Totales recalculados:', [
                'subtotal' => $this->quotation->subtotal,
                'total' => $this->quotation->total
            ]);

            return $items;
        });
    }
}
