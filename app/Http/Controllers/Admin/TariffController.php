<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Season;
use App\Models\Service;
use App\Models\SubCategory;
use App\Models\Tariff;
use Illuminate\Http\Request;

class TariffController extends Controller
{
    public function index($serviceId)
    {
        $service = Service::with(['supplier', 'category', 'descriptions.language'])->findOrFail($serviceId);

        $tariffsPaginated = Tariff::with('subcategory')
            ->where('id_service', $serviceId)
            ->whereNull('id_season')
            ->paginate(15);

        $groupedTariffs = $tariffsPaginated->getCollection()->groupBy('id_subcategories');

        $paginator = $tariffsPaginated->setCollection($groupedTariffs);

        $allTariffs = Tariff::where('id_service', $serviceId)
            ->whereNull('id_season')
            ->get();
        $existingSubcategoryIds = $allTariffs->pluck('id_subcategories')->unique()->toArray();

        $availableSubcategories = SubCategory::where('status', 'active')
            ->where('id_category', $service->id_category)
            ->whereNotIn('id_subcategories', $existingSubcategoryIds)
            ->get();

        $languages = Language::where('status', 'active')->orderBy('name')->get();

        return view('admin.tariffs.index', compact('service', 'paginator', 'availableSubcategories', 'languages'));
    }

    public function show($serviceId)
    {
        $service = Service::with(['supplier', 'category'])->findOrFail($serviceId);

        $tariffs = Tariff::with('subcategory')
            ->where('id_service', $serviceId)
            ->get();

        $subcategories = SubCategory::where('status', 'active')
            ->where('id_category', $service->id_category)
            ->get();

        return view('admin.tariffs.show', compact('service', 'tariffs', 'subcategories'));
    }

    public function store(Request $request, $serviceId)
    {
        $rules = [
            'id_subcategories' => 'required|array',
            'id_subcategories.*' => 'exists:sub_categorie,id_subcategories',
        ];

        $request->validate($rules);

        $service = Service::findOrFail($serviceId);

        $existingSubcategories = Tariff::where('id_service', $serviceId)
            ->whereNull('id_season')
            ->pluck('id_subcategories')
            ->toArray();

        $created = 0;
        foreach ($request->id_subcategories as $idSub) {
            if (in_array($idSub, $existingSubcategories)) {
                continue;
            }

            Tariff::create([
                'id_service' => $serviceId,
                'id_subcategories' => $idSub,
                'pricing_type' => 'flat',
                'price' => null,
                'min_people_count' => 0,
                'max_people_count' => 0,
                'status' => 'pending',
            ]);
            $created++;
        }

        $message = $created > 0
            ? "{$created} subcategoría(s) registrada(s). Ahora asigna el precio en cada una."
            : 'Todas las subcategorías ya tienen tarifa asignada.';

        // If the service has no availability days set, default to full week (Lunes a Domingo)
        try {
            $service = Service::find($serviceId);
            if ($service && empty(trim((string) $service->availability_days))) {
                $weekDays = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
                $service->availability_days = implode(', ', $weekDays);
                $service->save();
            }
        } catch (\Exception $e) {
            // Non-fatal: don't block the flow if availability update fails
        }

        return redirect()
            ->route('admin.tariffs.index', $serviceId)
            ->with('success', $message);
    }

    /**
     * Mostrar formulario de edición de una tarifa específica
     * GET: /servicios/{service}/tarifas/{tariff}/edit
     */
    public function edit($serviceId, $tariffId)
    {
        $service = Service::with(['supplier', 'category'])->findOrFail($serviceId);

        $tariff = Tariff::with('subcategory')
            ->where('id_service', $serviceId)
            ->where('id_tariff', $tariffId)
            ->firstOrFail();

        return view('admin.tariffs.show', compact('service', 'tariff'));
    }

    /**
     * Editar una subcategoría completa (todos sus rangos y temporadas)
     * GET: /servicios/{service}/tarifas/subcategoria/{subcategory}/editar
     */
    public function editSubcategory($serviceId, $subcategoryId)
    {
        $service = Service::with(['supplier', 'category'])->findOrFail($serviceId);

        $subcategory = SubCategory::findOrFail($subcategoryId);

        // Rangos base (sin temporada)
        $ranges = Tariff::where('id_service', $serviceId)
            ->where('id_subcategories', $subcategoryId)
            ->whereNull('id_season')
            ->get();

        $tariff = $ranges->first();

        $seasons = Season::whereHas('tariffs', function ($query) use ($serviceId, $subcategoryId) {
            $query->where('id_service', $serviceId)
                ->where('id_subcategories', $subcategoryId);
        })
            ->orderBy('start_date', 'asc')
            ->get();

        $seasonTariffsGrouped = Tariff::where('id_service', $serviceId)
            ->where('id_subcategories', $subcategoryId)
            ->whereNotNull('id_season')
            ->get()
            ->groupBy('id_season');

        $assignedSeasonIds = $seasonTariffsGrouped->keys();
        $availableSeasons = Season::whereHas('tariffs', function ($query) use ($serviceId) {
            $query->where('id_service', $serviceId);
        })
            ->whereNotIn('id_season', $assignedSeasonIds)
            ->orderBy('start_date')
            ->get();

        return view('admin.tariffs.show', compact(
            'service',
            'tariff',
            'ranges',
            'subcategory',
            'seasons',
            'seasonTariffsGrouped',
            'availableSeasons'
        ));
    }

    /**
     * Actualizar una tarifa específica (modo FLAT)
     * PUT: /servicios/{service}/tarifas/{tariff}
     */
    public function update(Request $request, $serviceId, $tariffId)
    {
        $service = Service::findOrFail($serviceId);

        $tariff = Tariff::where('id_service', $serviceId)
            ->where('id_tariff', $tariffId)
            ->firstOrFail();

        $rules = [
            'price' => 'required|numeric|min:0',
            'status' => 'nullable|string|in:active,pending,inactive',
        ];

        $request->validate($rules);

        $tariff->update([
            'price' => $request->price,
            'pricing_type' => 'flat',
            'min_people_count' => 0,
            'max_people_count' => 0,
            'status' => $request->status ?? 'pending',
        ]);

        return redirect()
            ->route('admin.tariffs.index', $serviceId)
            ->with('success', 'Tarifa actualizada exitosamente.');
    }

    public function updateFlat(Request $request, $serviceId, $subcategoryId)
    {
        $request->validate([
            'price' => 'required|numeric|min:0',
            'status' => 'nullable|string|in:active,pending,inactive',
        ]);

        $service = Service::findOrFail($serviceId);
        $subcategory = SubCategory::where('id_subcategories', $subcategoryId)
            ->where('id_category', $service->id_category)
            ->firstOrFail();

        Tariff::where('id_service', $serviceId)
            ->where('id_subcategories', $subcategory->id_subcategories)
            ->whereNull('id_season')
            ->delete();

        Tariff::create([
            'id_service' => $serviceId,
            'id_subcategories' => $subcategoryId,
            'pricing_type' => 'flat',
            'min_people_count' => 0,
            'max_people_count' => 0,
            'price' => $request->price,
            'status' => $request->status ?? 'active',
        ]);

        return redirect()
            ->route('admin.tariffs.editSubcategory', [$serviceId, $subcategoryId])
            ->with('success', 'Precio único actualizado exitosamente.');
    }

    /**
     * Actualizar múltiples rangos para una subcategoría (modo TIERED)
     * PUT: /servicios/{service}/tarifas/subcategoria/{subcategory}/rangos
     */
    public function updateRanges(Request $request, $serviceId, $subcategoryId)
    {
        $service = Service::findOrFail($serviceId);

        $request->validate([
            'ranges' => 'required|array|min:1',
            'ranges.*.min' => 'required|integer|min:0',
            'ranges.*.max' => 'nullable|integer|min:0',
            'ranges.*.price' => 'required|numeric|min:0',
            'status' => 'nullable|string|in:active,pending,inactive',
        ]);

        // Eliminar rangos existentes para esta subcategoría
        Tariff::where('id_service', $serviceId)
            ->where('id_subcategories', $subcategoryId)
            ->whereNull('id_season')
            ->delete();

        // Crear nuevos rangos
        foreach ($request->ranges as $range) {
            Tariff::create([
                'id_service' => $serviceId,
                'id_subcategories' => $subcategoryId,
                'pricing_type' => 'tiered',
                'min_people_count' => $range['min'],
                'max_people_count' => $range['max'] ?? null,
                'price' => $range['price'],
                'status' => $request->status ?? 'active',
            ]);
        }

        return redirect()
            ->route('admin.tariffs.index', $serviceId)
            ->with('success', 'Rangos actualizados exitosamente.');
    }

    /**
     * Actualizar precios de múltiples tarifas (edición masiva)
     * PUT: /servicios/{service}/tarifas/precios
     */
    public function updatePrice(Request $request, $serviceId)
    {
        $request->validate([
            'tariffs' => 'required|array',
            'tariffs.*.id' => 'required|exists:tariff,id_tariff',
            'tariffs.*.price' => 'required|numeric|min:0',
            'tariffs.*.min_people_count' => 'nullable|integer|min:0',
            'tariffs.*.max_people_count' => 'nullable|integer|min:0',
            'tariffs.*.status' => 'nullable|string|in:active,pending,inactive',
        ]);

        foreach ($request->tariffs as $tariffData) {
            $tariff = Tariff::where('id_service', $serviceId)
                ->where('id_tariff', $tariffData['id'])
                ->firstOrFail();

            $tariff->update([
                'price' => $tariffData['price'],
                'min_people_count' => $service->pricing_type === 'flat' ? 0 : ($tariffData['min_people_count'] ?? null),
                'max_people_count' => $service->pricing_type === 'flat' ? 0 : ($tariffData['max_people_count'] ?? null),
                'status' => $tariffData['status'] ?? 'active',
            ]);
        }

        return redirect()
            ->route('admin.tariffs.index', $serviceId)
            ->with('success', 'Todos los precios fueron actualizados exitosamente.');
    }

    /**
     * Eliminar una tarifa
     * DELETE: /servicios/{service}/tarifas/{tariff}
     */
    public function destroy($serviceId, $tariffId)
    {
        $tariff = Tariff::where('id_service', $serviceId)
            ->where('id_tariff', $tariffId)
            ->firstOrFail();

        $tariff->delete();

        return redirect()
            ->route('admin.tariffs.index', $serviceId)
            ->with('success', 'Tarifa eliminada exitosamente.');
    }

    /**
     * Eliminar todos los rangos de una subcategoría
     * DELETE: /servicios/{service}/tarifas/subcategoria/{subcategory}
     */
    public function destroySubcategory($serviceId, $subcategoryId)
    {
        Tariff::where('id_service', $serviceId)
            ->where('id_subcategories', $subcategoryId)
            ->delete();

        return redirect()
            ->route('admin.tariffs.index', $serviceId)
            ->with('success', 'Subcategoría y sus rangos eliminados exitosamente.');
    }
}
