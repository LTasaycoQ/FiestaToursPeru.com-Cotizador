<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Season;
use App\Models\Service;
use App\Models\Tariff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SeasonController extends Controller
{
    /**
     * Crear una nueva temporada
     */
    public function store(Request $request, $serviceId)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'id_subcategories' => 'required|exists:sub_categorie,id_subcategories',
        ]);

        $service = Service::with('category')->findOrFail($serviceId);
        $subcategoryId = $request->integer('id_subcategories');
        $baseTariffs = Tariff::where('id_service', $serviceId)
            ->where('id_subcategories', $subcategoryId)
            ->whereNull('id_season')
            ->get();

        if ($baseTariffs->isEmpty()) {
            return redirect()
                ->route('admin.tariffs.editSubcategory', [$serviceId, $subcategoryId])
                ->with('error', 'Registra primero la tarifa base de esta subcategoría.');
        }

        $season = DB::transaction(function () use ($request, $serviceId, $subcategoryId, $baseTariffs) {
            $season = Season::create([
                'id_service' => $serviceId,
                'name' => $request->name,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => 'active',
            ]);

            foreach ($baseTariffs as $baseTariff) {
                Tariff::create([
                    'id_service' => $serviceId,
                    'id_subcategories' => $subcategoryId,
                    'id_season' => $season->id_season,
                    'pricing_type' => $baseTariff->pricing_type,
                    'min_people_count' => $baseTariff->pricing_type === 'tiered' ? $baseTariff->min_people_count : 0,
                    'max_people_count' => $baseTariff->pricing_type === 'tiered' ? $baseTariff->max_people_count : 0,
                    'price' => null,
                    'status' => 'pending',
                ]);
            }

            return $season;
        });

        $redirectRoute = $request->filled('id_subcategories')
            ? route('admin.tariffs.editSubcategory', [$serviceId, $request->id_subcategories])
            : route('admin.tariffs.index', $serviceId);

        return redirect($redirectRoute)
            ->with('success', 'Temporada creada exitosamente.');
    }

    /**
     * Asignar una temporada existente a una subcategoría.
     */
    public function assignToSubcategory($serviceId, $seasonId, $subcategoryId)
    {
        $service = Service::with('category')->findOrFail($serviceId);
        $subcategory = $service->category?->subCategories()
            ->where('id_subcategories', $subcategoryId)
            ->firstOrFail();
        $season = Season::where('id_season', $seasonId)
            ->whereHas('tariffs', function ($query) use ($serviceId) {
                $query->where('id_service', $serviceId);
            })
            ->firstOrFail();

        $baseTariffs = Tariff::where('id_service', $serviceId)
            ->where('id_subcategories', $subcategory->id_subcategories)
            ->whereNull('id_season')
            ->get();

        if ($baseTariffs->isEmpty()) {
            return redirect()
                ->route('admin.tariffs.editSubcategory', [$serviceId, $subcategoryId])
                ->with('error', 'Registra primero la tarifa base de esta subcategoría.');
        }

        $alreadyAssigned = Tariff::where('id_service', $serviceId)
            ->where('id_subcategories', $subcategoryId)
            ->where('id_season', $seasonId)
            ->exists();

        if ($alreadyAssigned) {
            return redirect()
                ->route('admin.tariffs.editSubcategory', [$serviceId, $subcategoryId])
                ->with('error', 'Esta temporada ya está asignada a la subcategoría.');
        }

        DB::transaction(function () use ($baseTariffs, $serviceId, $subcategoryId, $seasonId) {
            foreach ($baseTariffs as $baseTariff) {
                Tariff::create([
                    'id_service' => $serviceId,
                    'id_subcategories' => $subcategoryId,
                    'id_season' => $seasonId,
                    'pricing_type' => $baseTariff->pricing_type,
                    'min_people_count' => $baseTariff->pricing_type === 'tiered' ? $baseTariff->min_people_count : 0,
                    'max_people_count' => $baseTariff->pricing_type === 'tiered' ? $baseTariff->max_people_count : 0,
                    'price' => null,
                    'status' => 'pending',
                ]);
            }
        });

        return redirect()
            ->route('admin.tariffs.editSubcategory', [$serviceId, $subcategoryId])
            ->with('success', 'Temporada asignada. Ahora registra sus precios.');
    }

    /**
     * Actualizar las tarifas de una temporada
     */
    public function updateTariffs(Request $request, $serviceId, $seasonId)
    {
        $service = Service::with('category')->findOrFail($serviceId);
        $season = Season::where('id_season', $seasonId)
            ->whereHas('tariffs', function ($query) use ($serviceId) {
                $query->where('id_service', $serviceId);
            })
            ->firstOrFail();

        $rules = [
            'id_subcategories' => 'required|exists:sub_categorie,id_subcategories',
            'pricing_type' => 'required|in:flat,tiered',
            'tariffs' => 'required|array|min:1',
            'tariffs.*.price' => 'required|numeric|min:0',
        ];

        if ($request->input('pricing_type') === 'tiered') {
            $rules['tariffs.*.min'] = 'required|integer|min:0';
            $rules['tariffs.*.max'] = 'nullable|integer|min:0';
        }

        $validated = $request->validate($rules);
        $pricingType = $validated['pricing_type'];
        $subcategoryId = $validated['id_subcategories'];
        DB::transaction(function () use ($validated, $serviceId, $season, $subcategoryId, $pricingType) {
            Tariff::where('id_service', $serviceId)
                ->where('id_season', $season->id_season)
                ->where('id_subcategories', $subcategoryId)
                ->delete();

            foreach ($validated['tariffs'] as $data) {
                Tariff::create([
                    'id_service' => $serviceId,
                    'id_subcategories' => $subcategoryId,
                    'id_season' => $season->id_season,
                    'pricing_type' => $pricingType,
                    'min_people_count' => $pricingType === 'tiered' ? $data['min'] : 0,
                    'max_people_count' => $pricingType === 'tiered' ? ($data['max'] ?? null) : 0,
                    'price' => $data['price'],
                    'status' => 'active',
                ]);
            }
        });

        return redirect()
            ->route('admin.tariffs.editSubcategory', [$serviceId, $subcategoryId])
            ->with('success', 'Precios de temporada actualizados exitosamente.');
    }

    /**
     * Eliminar una temporada y sus tarifas asociadas
     */
    public function destroy($serviceId, $seasonId)
    {
        $season = Season::findOrFail($seasonId);

        // Obtener la subcategoría para redirigir
        $tariff = Tariff::where('id_season', $seasonId)->first();
        $subcategoryId = $tariff->id_subcategories ?? null;

        // Eliminar tarifas asociadas
        Tariff::where('id_season', $seasonId)->delete();

        $season->delete();

        $redirectRoute = $subcategoryId
            ? route('admin.tariffs.editSubcategory', [$serviceId, $subcategoryId])
            : route('admin.tariffs.index', $serviceId);

        return redirect($redirectRoute)
            ->with('success', 'Temporada eliminada exitosamente.');
    }
}
