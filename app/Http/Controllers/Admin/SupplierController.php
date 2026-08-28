<?php

namespace App\Http\Controllers\Admin;

use App\Exports\SuppliersExport;
use App\Http\Controllers\Controller;
use App\Imports\SuppliersImport;
use App\Models\Bank;
use App\Models\BankAccount;
use App\Models\CategorySupplier;
use App\Models\Chain;
use App\Models\Contact;
use App\Models\Country;
use App\Models\Labels;
use App\Models\Language;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\SubCategory;
use App\Models\Supplier;
use App\Models\SupplierDescription;
use App\Models\SupplierImage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class SupplierController extends Controller
{
    protected function applySearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('supplier_name', 'like', "%{$search}%")
                ->orWhere('business_name', 'like', "%{$search}%")
                ->orWhere('tax_code', 'like', "%{$search}%")
                ->orWhere('general_email', 'like', "%{$search}%")
                ->orWhere('general_phone', 'like', "%{$search}%")
                ->orWhere('address', 'like', "%{$search}%")
                ->orWhereHas('city', function ($cq) use ($search) {
                    $cq->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('category', function ($cq) use ($search) {
                    $cq->where('category_name', 'like', "%{$search}%");
                });
        });
    }

    protected function getLogoBase64(): string
    {
        $path = storage_path('app/public/logo-pdf.png');

        if (! file_exists($path)) {
            return '';
        }

        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);

        return 'data:image/'.$type.';base64,'.base64_encode($data);
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $category = $request->input('category');
        $chainId = $request->input('chain');
        $country = $request->input('country');
        $sort = $request->input('sort', 'newest');
        $viewMode = $request->input('view_mode', 'all');
        $filter = $request->input('filter', 'active');

        $categories = CategorySupplier::orderBy('category_name')
            ->pluck('category_name', 'id_categories_suppliers')
            ->toArray();

        $chains = Chain::orderBy('name')
            ->pluck('name', 'id_chain')
            ->toArray();

        $countries = Country::orderBy('name')
            ->pluck('name', 'id_countries')
            ->toArray();

        $baseQuery = Supplier::with(['category', 'bankAccounts.bank', 'contacts', 'city.country', 'chains']);

        switch ($filter) {
            case 'trashed':
                $baseQuery->onlyTrashed();
                break;
            case 'all':
                $baseQuery->withTrashed();
                break;
            case 'active':
            default:
                $baseQuery->whereNull('deleted_at');
                break;
        }

        $baseQuery->when($search, fn ($query, $search) => $this->applySearch($query, $search))
            ->when($category, function ($query, $category) {
                $query->where('id_categories_suppliers', $category);
            })
            ->when($country, function ($query, $country) {
                $query->whereHas('city.country', function ($q) use ($country) {
                    $q->where('id_countries', $country);
                });
            })
            ->when($sort, function ($query, $sort) {
                switch ($sort) {
                    case 'newest':
                        $query->orderBy('created_at', 'desc');
                        break;
                    case 'oldest':
                        $query->orderBy('created_at', 'asc');
                        break;
                    case 'az':
                        $query->orderBy('supplier_name', 'asc');
                        break;
                    case 'za':
                        $query->orderBy('supplier_name', 'desc');
                        break;
                    default:
                        $query->orderBy('created_at', 'desc');
                }
            });

        if ($chainId) {
            if ($chainId === 'independent') {
                $baseQuery->whereDoesntHave('chains');
            } else {
                $baseQuery->whereHas('chains', function ($q) use ($chainId) {
                    $q->where('chain.id_chain', $chainId);
                });
            }
        }

        if ($viewMode === 'chain') {
            $baseQuery->whereHas('chains');
        } elseif ($viewMode === 'independent') {
            $baseQuery->whereDoesntHave('chains');
        }

        $suppliers = $baseQuery->paginate(10)->withQueryString();

        $activeCount = Supplier::whereNull('deleted_at')->count();
        $trashedCount = Supplier::onlyTrashed()->count();
        $totalCount = Supplier::withTrashed()->count();

        $totalChain = Supplier::whereHas('chains')
            ->whereNull('deleted_at')
            ->when($search, fn ($query, $search) => $this->applySearch($query, $search))
            ->when($category, function ($query, $category) {
                $query->where('id_categories_suppliers', $category);
            })
            ->count();

        $totalIndependent = Supplier::whereDoesntHave('chains')
            ->whereNull('deleted_at')
            ->when($search, fn ($query, $search) => $this->applySearch($query, $search))
            ->when($category, function ($query, $category) {
                $query->where('id_categories_suppliers', $category);
            })
            ->count();

        return view('admin.suppliers.index', compact(
            'suppliers',
            'search',
            'category',
            'country',
            'countries',
            'sort',
            'categories',
            'chains',
            'chainId',
            'viewMode',
            'totalChain',
            'totalIndependent',
            'activeCount',
            'trashedCount',
            'totalCount',
            'filter'
        ));
    }

    public function create()
    {
        $categories = CategorySupplier::orderBy('category_name')->get();
        $banks = Bank::orderBy('bank_name')->get();
        $chains = Chain::orderBy('name')->get();

        return view('admin.suppliers.create', compact('categories', 'banks', 'chains'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_name' => 'required|string|max:100',
            'business_name' => 'nullable|string|max:150',
            'tax_code' => 'nullable|string|max:20',
            'general_phone' => 'nullable|string|max:20',
            'general_email' => 'nullable|email|max:120',
            'id_categories_suppliers' => 'nullable|exists:categories_suppliers,id_categories_suppliers',
            'new_category_name' => 'nullable|string|max:100',
            'id_cities' => 'nullable|exists:cities,id_cities',
            'chains' => 'nullable|array',
            'chains.*' => 'exists:chain,id_chain',
            'address' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'bank_accounts' => 'nullable|array',
            'bank_accounts.*.id_bank' => 'nullable|exists:bank,id_bank',
            'bank_accounts.*.account_number' => 'nullable|string|max:100',
            'bank_accounts.*.cci' => 'nullable|string|max:100',
            'bank_accounts.*.currency' => 'nullable|string|max:40',
            'contacts' => 'nullable|array',
            'contacts.*.name' => 'string|max:100',
            'contacts.*.last_names' => 'nullable|string|max:100',
            'contacts.*.email' => 'nullable|email|max:120',
            'contacts.*.qualification' => 'nullable|string|max:100',
            'contacts.*.first_phone' => 'nullable|string|max:20',
            'contacts.*.second_phone' => 'nullable|string|max:20',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $categoryId = $request->id_categories_suppliers;
            if ($request->filled('new_category_name')) {
                $cat = CategorySupplier::create(['category_name' => $request->new_category_name]);
                $categoryId = $cat->id_categories_suppliers;
            }

            $supplier = Supplier::create([
                'supplier_name' => $request->supplier_name,
                'business_name' => $request->business_name,
                'tax_code' => $request->tax_code,
                'general_phone' => $request->general_phone,
                'general_email' => $request->general_email,
                'id_cities' => $request->id_cities,
                'address' => $request->address,
                'description' => $request->description,
                'id_categories_suppliers' => $categoryId ?: null,
            ]);

            if ($request->has('chains') && is_array($request->chains)) {
                $chainsToSync = array_filter($request->chains);
                if (! empty($chainsToSync)) {
                    $supplier->chains()->sync($chainsToSync);
                }
            }

            if ($request->has('contacts') && is_array($request->contacts)) {
                $first = true;
                foreach ($request->contacts as $contactData) {
                    if (! empty($contactData['name'])) {
                        Contact::create([
                            'id_supplier' => $supplier->id_supplier,
                            'id_client' => null,
                            'name' => $contactData['name'],
                            'last_names' => $contactData['last_names'] ?? null,
                            'email' => $contactData['email'] ?? null,
                            'qualification' => $contactData['qualification'] ?? null,
                            'first_phone' => $contactData['first_phone'] ?? null,
                            'second_phone' => $contactData['second_phone'] ?? null,
                            'es_principal' => $first,
                            'Date_of_birth' => null,
                        ]);
                        $first = false;
                    }
                }
            }

            if ($request->has('bank_accounts') && is_array($request->bank_accounts)) {
                foreach ($request->bank_accounts as $account) {
                    if (! empty($account['id_bank']) && ! empty($account['account_number'])) {
                        BankAccount::create([
                            'id_supplier' => $supplier->id_supplier,
                            'id_bank' => $account['id_bank'],
                            'account_number' => $account['account_number'],
                            'cci' => $account['cci'] ?? null,
                            'currency' => $account['currency'] ?? null,
                        ]);
                    }
                }
            }

            if ($request->hasFile('images')) {
                $firstImage = true;
                foreach ($request->file('images') as $image) {
                    $path = $image->store('suppliers/'.$supplier->id_supplier, 'public');

                    SupplierImage::create([
                        'id_supplier' => $supplier->id_supplier,
                        'image_path' => $path,
                        'is_principal' => $firstImage,
                    ]);
                    $firstImage = false;
                }
            }

            DB::commit();

            return redirect()->route('admin.suppliers.index')
                ->with('success', 'Proveedor, contactos, cuentas bancarias, cadenas e imágenes creados correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Error al crear el proveedor: '.$e->getMessage())
                ->withInput();
        }
    }

    public function show(Supplier $supplier)
    {
        $supplier->load([
            'category',
            'city',
            'country',
            'chains',
            'contacts',
            'bankAccounts.bank',
            'images',
        ]);

        $services = Service::with(['category', 'labels'])
            ->where('id_supplier', $supplier->id_supplier)
            ->paginate(10)
            ->appends(['tab' => 'tab-services']);

        $categories = ServiceCategory::where('status', 'active')->get();
        $subCategories = SubCategory::where('status', 'active')->get();
        $labels = Labels::where('status', 'active')->get();
        $banks = Bank::orderBy('bank_name')->get();

        return view('admin.suppliers.products', compact(
            'supplier',
            'services',
            'categories',
            'subCategories',
            'labels',
            'banks'
        ));
    }

    public function edit(Supplier $supplier)
    {
        $categories = CategorySupplier::orderBy('category_name')->get();
        $banks = Bank::orderBy('bank_name')->get();
        $chains = Chain::orderBy('name')->get();
        $supplier->load(['bankAccounts.bank', 'city.country', 'chains', 'images', 'descriptions.language']);

        return view('admin.suppliers.edit', compact('supplier', 'categories', 'banks', 'chains'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'supplier_name' => 'required|string|max:100',
            'business_name' => 'nullable|string|max:150',
            'tax_code' => 'nullable|string|max:20',
            'general_phone' => 'nullable|string|max:20',
            'general_email' => 'nullable|email|max:120',
            'id_cities' => 'nullable|exists:cities,id_cities',
            'address' => 'nullable|string|max:255',
            'id_categories_suppliers' => 'nullable|exists:categories_suppliers,id_categories_suppliers',
            'new_category_name' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'chains' => 'nullable|array',
            'chains.*' => 'exists:chain,id_chain',
            'bank_accounts' => 'nullable|array',
            'bank_accounts.*.id_bank' => 'required_with:bank_accounts.*.account_number|exists:bank,id_bank',
            'bank_accounts.*.account_number' => 'required_with:bank_accounts.*.id_bank|string|max:100',
            'bank_accounts.*.cci' => 'nullable|string|max:100',
            'bank_accounts.*.currency' => 'nullable|string|max:40',
            'delete_bank_accounts' => 'nullable|array',
            'delete_bank_accounts.*' => 'exists:bank_account,id_bank_account',
            'contacts' => 'nullable|array',
            'contacts.*.id' => 'nullable|integer',
            'contacts.*.name' => 'required_with:contacts|string|max:100',
            'contacts.*.last_names' => 'nullable|string|max:100',
            'contacts.*.email' => 'nullable|email|max:120',
            'contacts.*.qualification' => 'nullable|string|max:100',
            'contacts.*.first_phone' => 'nullable|string|max:20',
            'contacts.*.second_phone' => 'nullable|string|max:20',
            'delete_contacts' => 'nullable|array',
            'delete_contacts.*' => 'integer|exists:contacts,id_contacts',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'delete_images' => 'nullable|array',
            'delete_images.*' => 'exists:supplier_images,id_supplier_image',
        ]);

        try {
            DB::beginTransaction();

            $categoryId = $request->id_categories_suppliers;
            if ($request->filled('new_category_name')) {
                $cat = CategorySupplier::create(['category_name' => $request->new_category_name]);
                $categoryId = $cat->id_categories_suppliers;
            }

            $supplier->update([
                'supplier_name' => $request->supplier_name,
                'business_name' => $request->business_name,
                'tax_code' => $request->tax_code,
                'general_phone' => $request->general_phone,
                'general_email' => $request->general_email,
                'id_cities' => $request->id_cities,
                'address' => $request->address,
                'description' => $request->description,
                'id_categories_suppliers' => $categoryId ?: null,
            ]);

            if ($request->has('chains')) {
                $chainsToSync = is_array($request->chains) ? array_filter($request->chains) : [];
                $supplier->chains()->sync($chainsToSync);
            } else {
                $supplier->chains()->sync([]);
            }

            if ($request->has('delete_contacts') && ! empty($request->delete_contacts)) {
                Contact::whereIn('id_contacts', $request->delete_contacts)
                    ->where('id_supplier', $supplier->id_supplier)
                    ->delete();
            }

            if ($request->has('contacts') && is_array($request->contacts)) {
                $deleteIds = $request->input('delete_contacts', []);
                foreach ($request->contacts as $contactData) {
                    if (empty($contactData['name'])) {
                        continue;
                    }

                    if (isset($contactData['id']) && ! empty($contactData['id'])) {
                        if (in_array($contactData['id'], $deleteIds)) {
                            continue;
                        }

                        $contact = Contact::where('id_contacts', $contactData['id'])
                            ->where('id_supplier', $supplier->id_supplier)
                            ->first();

                        if ($contact) {
                            $contact->update([
                                'name' => $contactData['name'],
                                'last_names' => $contactData['last_names'] ?? null,
                                'email' => $contactData['email'] ?? null,
                                'qualification' => $contactData['qualification'] ?? null,
                                'first_phone' => $contactData['first_phone'] ?? null,
                                'second_phone' => $contactData['second_phone'] ?? null,
                                'es_principal' => isset($contactData['es_principal']) && $contactData['es_principal'] == 1,
                            ]);
                        }
                    } else {
                        Contact::create([
                            'id_supplier' => $supplier->id_supplier,
                            'id_client' => null,
                            'name' => $contactData['name'],
                            'last_names' => $contactData['last_names'] ?? null,
                            'email' => $contactData['email'] ?? null,
                            'qualification' => $contactData['qualification'] ?? null,
                            'first_phone' => $contactData['first_phone'] ?? null,
                            'second_phone' => $contactData['second_phone'] ?? null,
                            'es_principal' => isset($contactData['es_principal']) && $contactData['es_principal'] == 1,
                            'Date_of_birth' => null,
                        ]);
                    }
                }
            }

            if ($request->has('delete_bank_accounts') && ! empty($request->delete_bank_accounts)) {
                BankAccount::whereIn('id_bank_account', $request->delete_bank_accounts)->delete();
            }

            if ($request->has('bank_accounts') && is_array($request->bank_accounts)) {
                foreach ($request->bank_accounts as $account) {
                    if (empty($account['id_bank']) || empty($account['account_number'])) {
                        continue;
                    }

                    if (isset($account['id_bank_account']) && ! empty($account['id_bank_account'])) {
                        $bankAccount = BankAccount::find($account['id_bank_account']);
                        if ($bankAccount) {
                            $bankAccount->update([
                                'id_bank' => $account['id_bank'],
                                'account_number' => $account['account_number'],
                                'cci' => $account['cci'] ?? null,
                                'currency' => $account['currency'] ?? null,
                            ]);
                        }
                    } else {
                        BankAccount::create([
                            'id_supplier' => $supplier->id_supplier,
                            'id_bank' => $account['id_bank'],
                            'account_number' => $account['account_number'],
                            'cci' => $account['cci'] ?? null,
                            'currency' => $account['currency'] ?? null,
                        ]);
                    }
                }
            }

            $principales = Contact::where('id_supplier', $supplier->id_supplier)
                ->where('es_principal', true)
                ->get();

            if ($principales->count() > 1) {
                $principales->skip(1)->each(function ($contact) {
                    $contact->update(['es_principal' => false]);
                });
            }

            if ($request->has('delete_images')) {
                foreach ($request->delete_images as $imageId) {
                    $image = SupplierImage::find($imageId);
                    if ($image) {
                        Storage::disk('public')->delete($image->image_path);
                        $image->delete();
                    }
                }
            }

            if ($request->hasFile('images')) {
                $hasImages = $supplier->images()->count() > 0;
                $firstNewImage = ! $hasImages;

                foreach ($request->file('images') as $image) {
                    $path = $image->store('suppliers/'.$supplier->id_supplier, 'public');

                    SupplierImage::create([
                        'id_supplier' => $supplier->id_supplier,
                        'image_path' => $path,
                        'is_principal' => $firstNewImage,
                    ]);
                    $firstNewImage = false;
                }
            }

            if ($supplier->images()->count() > 0) {
                $principal = $supplier->images()->where('is_principal', true)->first();
                if (! $principal) {
                    $firstImage = $supplier->images()->first();
                    if ($firstImage) {
                        $firstImage->is_principal = true;
                        $firstImage->save();
                    }
                }
            }

            DB::commit();

            return redirect()->route('admin.suppliers.index')
                ->with('success', 'Proveedor, contactos, cuentas bancarias, cadenas e imágenes actualizados correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Error al actualizar el proveedor: '.$e->getMessage())
                ->withInput();
        }
    }

    public function storeDescription(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'id_language' => 'required|exists:languages,id_language',
            'description' => 'required|string|max:5000',
        ]);

        SupplierDescription::updateOrCreate(
            [
                'id_supplier' => $supplier->id_supplier,
                'id_language' => $validated['id_language'],
            ],
            ['description' => trim($validated['description'])]
        );

        return redirect()
            ->route('admin.suppliers.productos', ['supplier' => $supplier->id_supplier, 'tab' => 'tab-info'])
            ->with('success', 'Descripción guardada correctamente.');
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier->trashed()) {
            $supplier->forceDelete();

            return back()->with('success', 'Proveedor eliminado permanentemente.');
        }

        $supplier->delete();

        return back()->with('success', 'Proveedor eliminado correctamente.');
    }

    public function restore(Supplier $supplier)
    {
        if ($supplier->trashed()) {
            $supplier->restore();

            return back()->with('success', 'Proveedor restaurado correctamente.');
        }

        return back()->with('warning', 'El proveedor ya está activo.');
    }

    public function forceDestroy(Supplier $supplier)
    {
        $supplier->forceDelete();

        return back()->with('success', 'Proveedor eliminado permanentemente.');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:suppliers,id_supplier',
        ]);

        $ids = $request->ids;

        $activeSuppliers = Supplier::whereIn('id_supplier', $ids)
            ->whereNull('deleted_at')
            ->get();

        $trashedSuppliers = Supplier::withTrashed()
            ->whereIn('id_supplier', $ids)
            ->whereNotNull('deleted_at')
            ->get();

        $activeCount = $activeSuppliers->count();
        $trashedCount = $trashedSuppliers->count();
        $totalCount = $activeCount + $trashedCount;

        if ($totalCount === 0) {
            return redirect()->route('admin.suppliers.index')
                ->with('warning', 'No se encontraron proveedores para eliminar.');
        }

        foreach ($activeSuppliers as $supplier) {
            $supplier->delete();
        }

        foreach ($trashedSuppliers as $supplier) {
            $supplier->forceDelete();
        }

        $message = "{$activeCount} proveedor(es) eliminado(s) correctamente.";
        if ($trashedCount > 0) {
            $message .= " {$trashedCount} proveedor(es) eliminado(s) permanentemente.";
        }

        return redirect()->route('admin.suppliers.index')
            ->with('success', $message);
    }

    public function bulkRestore(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:suppliers,id_supplier',
        ]);

        $count = Supplier::withTrashed()
            ->whereIn('id_supplier', $request->ids)
            ->whereNotNull('deleted_at')
            ->restore();

        if ($count === 0) {
            return redirect()->route('admin.suppliers.index')
                ->with('warning', 'No se encontraron proveedores eliminados para restaurar.');
        }

        return redirect()->route('admin.suppliers.index')
            ->with('success', "{$count} proveedor(es) restaurado(s) correctamente.");
    }

    public function bulkForceDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:suppliers,id_supplier',
        ]);

        $count = Supplier::withTrashed()
            ->whereIn('id_supplier', $request->ids)
            ->forceDelete();

        if ($count === 0) {
            return redirect()->route('admin.suppliers.index')
                ->with('warning', 'No se encontraron proveedores para eliminar permanentemente.');
        }

        return redirect()->route('admin.suppliers.index')
            ->with('success', "{$count} proveedor(es) eliminado(s) permanentemente.");
    }

    public function storeBank(Request $request)
    {
        $request->validate([
            'bank_name' => 'required|string|max:50|unique:bank,bank_name',
        ], [
            'bank_name.required' => 'El nombre del banco es obligatorio.',
            'bank_name.unique' => 'Ya existe un banco registrado con ese nombre.',
        ]);

        $bank = Bank::create([
            'bank_name' => trim($request->bank_name),
        ]);

        return response()->json([
            'success' => true,
            'bank' => [
                'id' => $bank->id_bank,
                'name' => $bank->bank_name,
            ],
        ]);
    }

    public function uploadImages(Request $request, $supplierId)
    {
        $supplier = Supplier::findOrFail($supplierId);

        $request->validate([
            'images.*' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $uploadedImages = [];

        foreach ($request->file('images') as $image) {
            $path = $image->store('suppliers/'.$supplierId, 'public');

            $supplierImage = SupplierImage::create([
                'id_supplier' => $supplierId,
                'image_path' => $path,
                'is_principal' => ! $supplier->hasImages(),
            ]);

            $uploadedImages[] = $supplierImage;
        }

        return redirect()->route('admin.suppliers.show', $supplierId)
            ->with('success', count($uploadedImages).' imagen(es) subida(s) exitosamente');
    }

    public function setPrincipalImage($imageId)
    {
        $image = SupplierImage::findOrFail($imageId);
        $image->is_principal = true;
        $image->save();

        return redirect()->back()->with('success', 'Imagen principal actualizada');
    }

    public function deleteImage($imageId)
    {
        $image = SupplierImage::findOrFail($imageId);

        Storage::disk('public')->delete($image->image_path);

        $wasPrincipal = $image->is_principal;
        $supplierId = $image->id_supplier;

        $image->delete();

        if ($wasPrincipal) {
            $remainingImage = SupplierImage::where('id_supplier', $supplierId)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($remainingImage) {
                $remainingImage->is_principal = true;
                $remainingImage->save();
            }
        }

        return redirect()->back()->with('success', 'Imagen eliminada exitosamente');
    }

    // ============================================================
    // EXPORTACIONES
    // ============================================================

    public function exportExcel(Request $request)
    {
        $search = $request->input('search');
        $category = $request->input('category');
        $sort = $request->input('sort', 'newest');

        if ($request->has('supplier_id') && ! empty($request->supplier_id)) {
            $supplier = Supplier::with(['category', 'contacts', 'bankAccounts.bank', 'city'])
                ->find($request->supplier_id);

            if (! $supplier) {
                return back()->with('error', 'Proveedor no encontrado');
            }

            $suppliers = collect([$supplier]);
            $filename = 'proveedor_'.str($supplier->supplier_name)->slug().'_'.now()->format('Ymd').'.xlsx';
        } else {
            $suppliers = Supplier::with(['category', 'contacts', 'bankAccounts.bank', 'city'])
                ->whereNull('deleted_at')
                ->when($search, fn ($query, $search) => $this->applySearch($query, $search))
                ->when($category, function ($query, $category) {
                    $query->where('id_categories_suppliers', $category);
                })
                ->when($sort, function ($query, $sort) {
                    switch ($sort) {
                        case 'newest':
                            $query->orderBy('created_at', 'desc');
                            break;
                        case 'oldest':
                            $query->orderBy('created_at', 'asc');
                            break;
                        case 'az':
                            $query->orderBy('supplier_name', 'asc');
                            break;
                        case 'za':
                            $query->orderBy('supplier_name', 'desc');
                            break;
                        default:
                            $query->orderBy('created_at', 'desc');
                    }
                })
                ->get();

            $filename = 'proveedores';
            if ($category) {
                $categoryName = CategorySupplier::find($category)?->category_name;
                if ($categoryName) {
                    $filename .= '_'.str($categoryName)->slug();
                }
            }
            $filename .= '_'.now()->format('Ymd').'.xlsx';
        }

        return Excel::download(new SuppliersExport($suppliers), $filename);
    }

    public function exportPdf(Request $request, ?Supplier $supplier = null)
    {
        if ($request->has('supplier_id') && ! empty($request->supplier_id)) {
            $supplier = Supplier::with(['category', 'contacts', 'bankAccounts.bank', 'city'])
                ->find($request->supplier_id);

            if (! $supplier) {
                abort(404, 'Proveedor no encontrado');
            }

            $suppliers = collect([$supplier]);
            $filename = 'proveedor_'.str($supplier->supplier_name)->slug().'.pdf';
        } elseif ($supplier) {
            $supplier->load(['category', 'contacts', 'bankAccounts.bank', 'city']);
            $suppliers = collect([$supplier]);
            $filename = 'proveedor_'.str($supplier->supplier_name)->slug().'.pdf';
        } else {
            $search = $request->input('search');
            $category = $request->input('category');
            $sort = $request->input('sort', 'newest');

            $suppliers = Supplier::with(['category', 'contacts', 'bankAccounts.bank', 'city'])
                ->whereNull('deleted_at')
                ->when($search, fn ($query, $search) => $this->applySearch($query, $search))
                ->when($category, function ($query, $category) {
                    $query->where('id_categories_suppliers', $category);
                })
                ->when($sort, function ($query, $sort) {
                    switch ($sort) {
                        case 'newest':
                            $query->orderBy('created_at', 'desc');
                            break;
                        case 'oldest':
                            $query->orderBy('created_at', 'asc');
                            break;
                        case 'az':
                            $query->orderBy('supplier_name', 'asc');
                            break;
                        case 'za':
                            $query->orderBy('supplier_name', 'desc');
                            break;
                        default:
                            $query->orderBy('created_at', 'desc');
                    }
                })
                ->get();

            $filename = 'proveedores';
            if ($category) {
                $categoryName = CategorySupplier::find($category)?->category_name;
                if ($categoryName) {
                    $filename .= '_'.str($categoryName)->slug();
                }
            }
            $filename .= '_'.now()->format('Ymd').'.pdf';
        }

        $pdf = Pdf::loadView('admin.suppliers.pdf', [
            'suppliers' => $suppliers,
            'logoBase64' => $this->getLogoBase64(),
        ])
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont' => 'Arial',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
                'dpi' => 96,
                'isPhpEnabled' => true,
                'encoding' => 'UTF-8',
            ]);

        return $pdf->download($filename);
    }

    public function exportPdfAll(Request $request)
    {
        $search = $request->input('search');
        $category = $request->input('category');
        $sort = $request->input('sort', 'newest');

        $suppliers = Supplier::with(['category', 'contacts', 'bankAccounts.bank', 'city'])
            ->whereNull('deleted_at')
            ->when($search, fn ($query, $search) => $this->applySearch($query, $search))
            ->when($category, function ($query, $category) {
                $query->where('id_categories_suppliers', $category);
            })
            ->when($sort, function ($query, $sort) {
                switch ($sort) {
                    case 'newest':
                        $query->orderBy('created_at', 'desc');
                        break;
                    case 'oldest':
                        $query->orderBy('created_at', 'asc');
                        break;
                    case 'az':
                        $query->orderBy('supplier_name', 'asc');
                        break;
                    case 'za':
                        $query->orderBy('supplier_name', 'desc');
                        break;
                    default:
                        $query->orderBy('created_at', 'desc');
                }
            })
            ->get();

        $pdf = Pdf::loadView('admin.suppliers.pdfAll', [
            'suppliers' => $suppliers,
            'logoBase64' => $this->getLogoBase64(),
        ])
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont' => 'Arial',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
                'dpi' => 96,
                'isPhpEnabled' => true,
                'encoding' => 'UTF-8',
            ]);

        $filename = 'proveedores';
        if ($category) {
            $categoryName = CategorySupplier::find($category)?->category_name;
            if ($categoryName) {
                $filename .= '_'.str($categoryName)->slug();
            }
        }
        $filename .= '_'.now()->format('Ymd').'.pdf';

        return $pdf->stream($filename);
    }

    public function showBankAccounts(Supplier $supplier)
    {
        $supplier->load('bankAccounts.bank');

        return view('admin.suppliers.bank_accounts', compact('supplier'));
    }

    public function importView()
    {
        return view('admin.suppliers.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ], [
            'archivo.required' => 'Debes seleccionar un archivo.',
            'archivo.mimes' => 'Solo se aceptan archivos .xlsx, .xls o .csv.',
            'archivo.max' => 'El archivo no puede superar los 5MB.',
        ]);

        $import = new SuppliersImport;

        try {
            Excel::import($import, $request->file('archivo'));
        } catch (\Exception $e) {
            return back()->withErrors(['archivo' => 'Error al procesar el archivo: '.$e->getMessage()]);
        }

        $msg = "Importación completada: {$import->imported} proveedor(es) procesado(s).";
        if ($import->skipped > 0) {
            $msg .= " {$import->skipped} fila(s) omitida(s).";
        }
        if (! empty($import->errors)) {
            $msg .= ' Con errores: '.implode(' | ', $import->errors);
        }

        return redirect()->route('admin.suppliers.index')->with('success', $msg);
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="plantilla_proveedores.csv"',
        ];

        $columns = [
            'supplier_name',
            'business_name',
            'tax_code',
            'general_phone',
            'general_email',
            'city_name',
            'address',
            'category_name',
            'contact_name',
            'contact_last_names',
            'contact_email',
            'contact_qualification',
            'contact_first_phone',
            'contact_second_phone',
        ];

        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fputcsv($file, [
                'Proveedor Ejemplo',
                'Razón Social Ejemplo',
                '20123456789',
                '987654321',
                'contacto@ejemplo.com',
                'Lima',
                'Av. Ejemplo 123',
                'Hoteles',
                'Juan',
                'Pérez',
                'juan@ejemplo.com',
                'Gerente',
                '987654321',
                '987654322',
            ]);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function products(Request $request, Supplier $supplier)
    {
        $search = $request->input('search');
        $category = $request->input('category');
        $labels = Labels::where('status', 'active')->get();
        $status = $request->input('status');
        $sort = $request->input('sort', 'newest');

        $categories = ServiceCategory::where('status', 'active')
            ->orderBy('name')
            ->get();

        $statuses = Service::where('id_supplier', $supplier->id_supplier)
            ->distinct()
            ->pluck('status')
            ->filter()
            ->toArray();

        $services = Service::with(['category', 'labels', 'descriptions.language'])
            ->where('id_supplier', $supplier->id_supplier)
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name_service', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($category, function ($query, $category) {
                return $query->where('id_category', $category);
            })
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($sort, function ($query, $sort) {
                switch ($sort) {
                    case 'newest':
                        $query->orderBy('created_at', 'desc');
                        break;
                    case 'oldest':
                        $query->orderBy('created_at', 'asc');
                        break;
                    case 'az':
                        $query->orderBy('name_service', 'asc');
                        break;
                    case 'za':
                        $query->orderBy('name_service', 'desc');
                        break;
                    default:
                        $query->orderBy('created_at', 'desc');
                }
            })
            ->paginate(10)
            ->withQueryString()
            ->appends(['tab' => 'tab-services']);

        $supplier->load(['category', 'city', 'country', 'chains', 'descriptions.language']);

        $banks = Bank::orderBy('bank_name')->get();
        $languages = Language::where('status', 'active')->orderBy('name')->get();
        $labels = Labels::where('status', 'active')->get();

        return view('admin.suppliers.products', compact(
            'supplier',
            'services',
            'search',
            'category',
            'status',
            'sort',
            'categories',
            'statuses',
            'banks',
            'languages',
            'labels'
        ));
    }
}
