<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Labels;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\SubCategory;
use App\Models\Supplier;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $category = $request->input('category');
        $statusFilter = $request->input('status');

        $services = Service::with(['category', 'labels', 'supplier'])
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name_service', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($category, function ($query, $category) {
                return $query->where('id_category', $category);
            })
            ->when($labels, function ($query, $labels) {
                return $query->where('id_labels', $labels);
            })
            ->when($statusFilter, function ($query, $statusFilter) {
                return $query->where('status', $statusFilter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $categories = ServiceCategory::where('status', 'active')->get();
        $statuses = Service::distinct()->pluck('status')->filter()->toArray();

        return view('admin.services.index', compact(
            'services', 'categories', 'statuses', 'search', 'category', 'statusFilter'
        ));
    }

    public function create()
    {
        $categories = ServiceCategory::where('status', 'active')->get();
        $labels = Labels::where('status', 'active')->get();
        $suppliers = Supplier::whereNull('deleted_at')->orderBy('supplier_name')->get();

        return view('admin.services.create', compact('categories', 'labels', 'suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_service' => 'required|string|max:300',
            'id_supplier' => 'required|exists:suppliers,id_supplier',
            'id_category' => 'required|exists:service_category,id_category',
            'id_labels' => 'nullable|exists:labels,id_labels',
            'description' => 'nullable|string|max:900',
            'availability_days' => 'nullable|string|max:50',
            'status' => 'nullable|string|max:20',
        ]);

        $service = Service::create([
            'name_service' => $validated['name_service'],
            'id_supplier' => $validated['id_supplier'],
            'id_category' => $validated['id_category'],
            'id_labels' => $validated['id_labels'] ?? null,
            'description' => $validated['description'] ?? null,
            'availability_days' => $validated['availability_days'] ?? null,
            'pricing_type' => null,
            'status' => $validated['status'] ?? 'active',
        ]);

        return redirect()
            ->route('admin.tariffs.index', $service->id_service)
            ->with('success', 'Servicio creado. Ahora registra sus tarifas.');
    }

    public function edit($id)
    {
        $service = Service::with(['category', 'labels', 'supplier'])->findOrFail($id);
        $categories = ServiceCategory::where('status', 'active')->get();
        $labels = Labels::where('status', 'active')->get();
        $suppliers = Supplier::whereNull('deleted_at')->orderBy('supplier_name')->get();

        return view('admin.services.edit', compact('service', 'categories', 'labels', 'suppliers'));
    }

    public function update(Request $request, $id)
    {
        $service = Service::findOrFail($id);

        $validated = $request->validate([
            'name_service' => 'required|string|max:300',
            'id_category' => 'required|exists:service_category,id_category',
            'id_labels' => 'nullable|exists:labels,id_labels',             'description' => 'nullable|string|max:900',
            'availability_days' => 'nullable|string|max:50',
            'status' => 'nullable|string|max:20',
        ]);

        $service->update([
            'name_service' => $validated['name_service'],
            'id_category' => $validated['id_category'],
            'id_labels' => $validated['id_labels'] ?? null,            'description' => $validated['description'] ?? null,
            'availability_days' => $validated['availability_days'] ?? null,
            'status' => $validated['status'] ?? $service->status,
        ]);

        return redirect()
            ->route('admin.suppliers.show', ['supplier' => $service->id_supplier, 'tab' => 'tab-services'])
            ->with('success', 'Servicio actualizado exitosamente');
    }

    public function destroy($id)
    {
        $service = Service::findOrFail($id);
        $supplierId = $service->id_supplier;
        $service->delete();

        return redirect()
            ->route('admin.suppliers.show', ['supplier' => $supplierId, 'tab' => 'tab-services'])
            ->with('success', 'Servicio eliminado exitosamente');
    }

    public function updateAvailability(Request $request, $id)
    {
        try {
            $service = Service::findOrFail($id);

            $allowedDays = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
            $usesClosingDays = $request->boolean('closed_days_present');
            $inputDays = $usesClosingDays
                ? $request->input('closed_days', [])
                : $request->input('availability_days', []);
            $days = is_array($inputDays)
                ? $inputDays
                : array_filter(array_map('trim', explode(',', (string) $inputDays)));
            $invalidDays = array_diff($days, $allowedDays);

            if ($invalidDays !== []) {
                return response()->json([
                    'success' => false,
                    'message' => 'Los días seleccionados no son válidos.',
                ], 422);
            }

            $availableDays = $usesClosingDays
                ? array_diff($allowedDays, $days)
                : array_intersect($allowedDays, $days);

            $service->availability_days = implode(', ', array_values($availableDays));
            $service->save();

            return response()->json(['success' => true, 'message' => 'Días de operación actualizados correctamente']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: '.$e->getMessage()], 500);
        }
    }

    public function getSubcategoriesByCategory($categoryId)
    {
        $subcategories = SubCategory::where('id_category', $categoryId)
            ->where('status', 'active')
            ->get(['id_subcategories as id', 'name']);

        return response()->json($subcategories);
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:300|unique:service_category,name',
            'is_accommodation' => 'nullable|boolean',
        ]);

        $category = ServiceCategory::create([
            'name' => trim($request->name),
            'pricing_type' => 'flat',
            'is_accommodation' => $request->boolean('is_accommodation'),
            'status' => 'active',
        ]);

        return response()->json([
            'success' => true,
            'category' => [
                'id' => $category->id_category,
                'name' => $category->name,
                'pricing_type' => 'flat',
                'is_accommodation' => $category->is_accommodation,
            ],
        ]);
    }

    public function storeSubcategory(Request $request)
    {
        $request->validate([
            'id_category' => 'required|exists:service_category,id_category',
            'name' => 'required|string|max:300|unique:sub_categorie,name',
        ]);

        $subcategory = SubCategory::create([
            'id_category' => $request->id_category,
            'name' => trim($request->name),
            'status' => 'active',
        ]);

        return response()->json([
            'success' => true,
            'subcategory' => [
                'id' => $subcategory->id_subcategories,
                'name' => $subcategory->name,
                'id_category' => $subcategory->id_category,
            ],
        ]);
    }

    public function getItineraryServices(Request $request)
    {
        $query = Service::with(['labels', 'supplier', 'category'])
            ->where('status', 'active')
            ->whereHas('category', fn ($q) => $q->where('is_accommodation', false));

        if ($request->filled('category')) {
            $query->where('id_category', $request->category);
        }

        return response()->json(['success' => true, 'data' => $query->get()]);
    }

    public function getAccommodationServices(Request $request)
    {
        $query = Service::with(['labels', 'supplier', 'category', 'tariffs.subCategory'])
            ->where('status', 'active')
            ->whereHas('category', fn ($q) => $q->where('is_accommodation', true));

        if ($request->filled('supplier_id')) {
            $query->where('id_supplier', $request->supplier_id);
        }

        return response()->json(['success' => true, 'data' => $query->get()]);
    }
}
