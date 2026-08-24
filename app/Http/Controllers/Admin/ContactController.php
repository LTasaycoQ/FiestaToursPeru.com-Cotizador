<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Client;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ContactsExport;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $viewType = $request->input('view', 'clientes');
        $filter = $request->input('filter', 'active'); // active, trashed, all

        $query = Contact::with(['client', 'supplier']);

        // Aplicar filtro de eliminados
        switch ($filter) {
            case 'trashed':
                $query->onlyTrashed();
                break;
            case 'all':
                $query->withTrashed();
                break;
            case 'active':
            default:
                $query->whereNull('deleted_at');
                break;
        }

        // Filtrar por tipo
        if ($viewType === 'clientes') {
            $query->whereNotNull('id_client')->whereNull('id_supplier');
        } else {
            $query->whereNotNull('id_supplier')->whereNull('id_client');
        }

        // Búsqueda
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('last_names', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('first_phone', 'like', "%{$search}%")
                  ->orWhere('qualification', 'like', "%{$search}%");
            });
        }

        // Cliente específico (solo si es vista clientes)
        if ($viewType === 'clientes' && $client = $request->input('client')) {
            $query->where('id_client', $client);
        }

        // Proveedor específico (solo si es vista proveedores)
        if ($viewType === 'proveedores' && $supplier = $request->input('supplier')) {
            $query->where('id_supplier', $supplier);
        }

        // Principal
        if ($principal = $request->input('principal')) {
            $query->where('es_principal', $principal);
        }

        // Fecha
        if ($date = $request->input('date')) {
            $now = now();
            match ($date) {
                'today' => $query->whereDate('created_at', $now->toDateString()),
                'week'  => $query->whereDate('created_at', '>=', $now->copy()->startOfWeek()->toDateString()),
                'month' => $query->whereDate('created_at', '>=', $now->copy()->startOfMonth()->toDateString()),
                'year'  => $query->whereDate('created_at', '>=', $now->copy()->startOfYear()->toDateString()),
                default => null,
            };
        }

        // Orden
        match ($request->input('sort', 'newest')) {
            'oldest' => $query->orderBy('created_at', 'asc'),
            'az'     => $query->orderBy('name', 'asc'),
            'za'     => $query->orderBy('name', 'desc'),
            default  => $query->orderBy('created_at', 'desc'),
        };

        $contacts = $query->paginate(8)->withQueryString();

        // Contadores para la vista
        $activeCount = Contact::where(function($q) use ($viewType) {
                if ($viewType === 'clientes') {
                    $q->whereNotNull('id_client')->whereNull('id_supplier');
                } else {
                    $q->whereNotNull('id_supplier')->whereNull('id_client');
                }
            })->whereNull('deleted_at')->count();

        $trashedCount = Contact::onlyTrashed()->where(function($q) use ($viewType) {
                if ($viewType === 'clientes') {
                    $q->whereNotNull('id_client')->whereNull('id_supplier');
                } else {
                    $q->whereNotNull('id_supplier')->whereNull('id_client');
                }
            })->count();

        $totalCount = Contact::withTrashed()->where(function($q) use ($viewType) {
                if ($viewType === 'clientes') {
                    $q->whereNotNull('id_client')->whereNull('id_supplier');
                } else {
                    $q->whereNotNull('id_supplier')->whereNull('id_client');
                }
            })->count();

        $clients = Client::orderBy('name_client')->get(['id_client', 'name_client']);
        $suppliers = Supplier::orderBy('supplier_name')->get(['id_supplier', 'supplier_name']);

        return view('admin.contacts.index', compact('contacts', 'clients', 'suppliers', 'viewType', 'activeCount', 'trashedCount', 'totalCount', 'filter'));
    }

    public function create()
    {
        $clients   = Client::orderBy('name_client')->get();
        $suppliers = Supplier::orderBy('supplier_name')->get();
        return view('admin.contacts.create', compact('clients', 'suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:100',
            'last_names'    => 'nullable|string|max:100',
            'qualification' => 'nullable|string|max:30',
            'email'         => 'nullable|email|max:80',
            'first_phone'   => 'nullable|string|max:20',
            'second_phone'  => 'nullable|string|max:20',
            'id_client'     => 'nullable|exists:clients,id_client',
            'id_supplier'   => 'nullable|exists:suppliers,id_supplier',
            'es_principal'  => 'boolean',
        ]);

        try {
            $contact = Contact::create([
                ...$request->only([
                    'name','last_names', 'qualification',
                    'email','first_phone','second_phone','id_client','id_supplier',
                ]),
                'es_principal' => $request->boolean('es_principal'),
            ]);

            // ✅ Redirigir a la página de productos del proveedor (donde estás)
            if ($request->has('id_supplier') && $request->id_supplier) {
                return redirect()->route('admin.suppliers.productos', $request->id_supplier)
                    ->with('success', 'Contacto creado correctamente.')
                    ->with('tab', 'contacts'); // Para activar la pestaña de contactos
            }

            return redirect()->route('admin.contacts.index')
                ->with('success', 'Contacto creado correctamente.');

        } catch (\Exception $e) {
            if ($request->has('id_supplier') && $request->id_supplier) {
                return redirect()->route('admin.suppliers.productos', $request->id_supplier)
                    ->with('error', 'Error al crear el contacto: ' . $e->getMessage());
            }

            return back()->with('error', 'Error al crear el contacto: ' . $e->getMessage());
        }
    }

    /**
     * Editar un contacto (permite editar incluso si está eliminado)
     */
    public function edit(Contact $contact)
    {
        $clients   = Client::orderBy('name_client')->get();
        $suppliers = Supplier::orderBy('supplier_name')->get();
        return view('admin.contacts.edit', compact('contact', 'clients', 'suppliers'));
    }

    /**
     * Actualizar un contacto (permite actualizar incluso si está eliminado)
     */
    public function update(Request $request, Contact $contact)
    {
        $request->validate([
            'name'          => 'required|string|max:100',
            'last_names'    => 'nullable|string|max:100',
            'qualification' => 'nullable|string|max:30',
            'email'         => 'nullable|email|max:80',
            'first_phone'   => 'nullable|string|max:20',
            'second_phone'  => 'nullable|string|max:20',
            'id_client'     => 'nullable|exists:clients,id_client',
            'id_supplier'   => 'nullable|exists:suppliers,id_supplier',
            'es_principal'  => 'boolean',
        ]);

        $contact->update([
            ...$request->only([
                'name','last_names','qualification',
                'email','first_phone','second_phone','id_client','id_supplier',
            ]),
            'es_principal' => $request->boolean('es_principal'),
        ]);

        // ✅ Redirigir a la página de productos del proveedor si viene de ahí
        if ($request->has('id_supplier') && $request->id_supplier) {
            return redirect()->route('admin.suppliers.productos', $request->id_supplier)
                ->with('success', 'Contacto actualizado correctamente.');
        }

        return redirect()->route('admin.contacts.index')
            ->with('success', 'Contacto actualizado correctamente.');
    }

    /**
     * Eliminar un contacto (soft delete o force delete)
     * ✅ Recibe el modelo Contact gracias al Route Model Binding
     */
    public function destroy(Contact $contact)
    {
        if ($contact->trashed()) {
            $contact->forceDelete();
            return back()->with('success', 'Contacto eliminado permanentemente.');
        }

        $contact->delete();
        return back()->with('success', 'Contacto eliminado correctamente.');
    }

    /**
     * Restaurar un contacto eliminado
     * ✅ Recibe el modelo Contact gracias al Route Model Binding
     */
    public function restore(Contact $contact)
    {
        if ($contact->trashed()) {
            $contact->restore();
            return back()->with('success', 'Contacto restaurado correctamente.');
        }

        return back()->with('warning', 'El contacto ya está activo.');
    }

    /**
     * Eliminar permanentemente un contacto
     * ✅ Recibe el modelo Contact gracias al Route Model Binding
     */
    public function forceDestroy(Contact $contact)
    {
        $contact->forceDelete();
        return back()->with('success', 'Contacto eliminado permanentemente.');
    }

    /**
     * Eliminar múltiples contactos (activos → soft delete, eliminados → force delete)
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'integer|exists:contacts,id_contacts',
        ]);

        $ids = $request->ids;

        // Obtener contactos activos y eliminados por separado
        $activeContacts = Contact::whereIn('id_contacts', $ids)
            ->whereNull('deleted_at')
            ->get();

        $trashedContacts = Contact::withTrashed()
            ->whereIn('id_contacts', $ids)
            ->whereNotNull('deleted_at')
            ->get();

        $activeCount = $activeContacts->count();
        $trashedCount = $trashedContacts->count();
        $totalCount = $activeCount + $trashedCount;

        if ($totalCount === 0) {
            return redirect()->route('admin.contacts.index')
                ->with('warning', 'No se encontraron contactos para eliminar.');
        }

        // Soft delete a los activos
        foreach ($activeContacts as $contact) {
            $contact->delete();
        }

        // Force delete a los ya eliminados
        foreach ($trashedContacts as $contact) {
            $contact->forceDelete();
        }

        $message = "{$activeCount} contacto(s) eliminado(s) correctamente.";
        if ($trashedCount > 0) {
            $message .= " {$trashedCount} contacto(s) eliminado(s) permanentemente.";
        }

        return redirect()->route('admin.contacts.index')
            ->with('success', $message);
    }

    /**
     * Restaurar múltiples contactos eliminados
     */
    public function bulkRestore(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'integer|exists:contacts,id_contacts',
        ]);

        $count = Contact::withTrashed()
            ->whereIn('id_contacts', $request->ids)
            ->whereNotNull('deleted_at')
            ->restore();

        if ($count === 0) {
            return redirect()->route('admin.contacts.index')
                ->with('warning', 'No se encontraron contactos eliminados para restaurar.');
        }

        return redirect()->route('admin.contacts.index')
            ->with('success', "{$count} contacto(s) restaurado(s) correctamente.");
    }

    /**
     * Eliminar permanentemente múltiples contactos
     */
    public function bulkForceDestroy(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'integer|exists:contacts,id_contacts',
        ]);

        $count = Contact::withTrashed()
            ->whereIn('id_contacts', $request->ids)
            ->forceDelete();

        if ($count === 0) {
            return redirect()->route('admin.contacts.index')
                ->with('warning', 'No se encontraron contactos para eliminar permanentemente.');
        }

        return redirect()->route('admin.contacts.index')
            ->with('success', "{$count} contacto(s) eliminado(s) permanentemente.");
    }

    /**
     * Exportar contactos a Excel
     */
    public function exportExcel(Request $request)
    {
        $clientId = $request->filled('client_id') ? (int) $request->client_id : null;

        $export   = new ContactsExport($clientId);
        $filename = $clientId
            ? 'contactos_cliente_' . $clientId . '_' . now()->format('Ymd') . '.xlsx'
            : 'contactos_' . now()->format('Ymd') . '.xlsx';

        return Excel::download($export, $filename);
    }

    /**
     * ✅ OBTENER DATOS DE UN CONTACTO PARA EDICIÓN VIA AJAX
     * Este método es llamado desde el modal de edición en products.blade.php
     */
    public function editData(Contact $contact)
    {
        return response()->json([
            'success' => true,
            'contact' => [
                'id_contact' => $contact->id_contact,
                'name' => $contact->name,
                'last_names' => $contact->last_names,
                'qualification' => $contact->qualification,
                'email' => $contact->email,
                'first_phone' => $contact->first_phone,
                'second_phone' => $contact->second_phone,
                'es_principal' => $contact->es_principal,
            ]
        ]);
    }
}
