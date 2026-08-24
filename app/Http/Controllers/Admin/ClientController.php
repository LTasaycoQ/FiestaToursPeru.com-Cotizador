<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Contact;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ClientsExport;
use App\Exports\ClientsExportById;
use App\Imports\ClientsImport;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->input('filter', 'active');
        $query = Client::with(['city.country']);

        // Aplicar filtro de eliminados
        switch ($filter) {
            case 'trashed':
                $query->onlyTrashed(); // Solo eliminados
                break;
            case 'all':
                $query->withTrashed(); // Todos (incluye eliminados)
                break;
            case 'active':
            default:
                $query->whereNull('deleted_at'); // Solo activos (comportamiento por defecto)
                break;
        }

        // Búsqueda
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name_client', 'like', "%{$search}%")
                    ->orWhere('tax_code', 'like', "%{$search}%")
                    ->orWhere('general_email', 'like', "%{$search}%")
                    ->orWhere('general_phone', 'like', "%{$search}%")
                    ->orWhereHas('city', function($cq) use ($search) {
                        $cq->where('name', 'like', "%{$search}%")
                            ->orWhereHas('country', function($cq2) use ($search) {
                                $cq2->where('name', 'like', "%{$search}%");
                            });
                    });
            });
        }

        // ✅ Buscar por país (a través de city.country)
        if ($country = $request->input('country')) {
            $query->whereHas('city.country', function($q) use ($country) {
                $q->where('name', $country);
            });
        }

        // Buscar por ciudad
        if ($city = $request->input('city')) {
            $query->whereHas('city', function($q) use ($city) {
                $q->where('name', $city);
            });
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

        // Ordenamiento
        match ($request->input('sort', 'newest')) {
            'oldest' => $query->orderBy('created_at', 'asc')->orderBy('id_client', 'asc'),
            'az'     => $query->orderBy('name_client', 'asc')->orderBy('id_client', 'asc'),
            'za'     => $query->orderBy('name_client', 'desc')->orderBy('id_client', 'desc'),
            'tax-az' => $query->orderBy('tax_code', 'asc')->orderBy('id_client', 'asc'),
            'tax-za' => $query->orderBy('tax_code', 'desc')->orderBy('id_client', 'desc'),
            default  => $query->orderBy('created_at', 'desc')->orderBy('id_client', 'desc'),
        };

        $clients = $query->paginate(8)->withQueryString();

        // Obtener ciudades únicas para el filtro (solo de clientes activos para no confundir)
        $cities = Client::whereHas('city')
            ->whereNull('deleted_at')
            ->with('city')
            ->get()
            ->pluck('city.name')
            ->unique()
            ->filter()
            ->sort()
            ->values()
            ->toArray();

        // Para exportar solo clientes activos (o todos según necesidad)
        $allClientsForExport = Client::whereNull('deleted_at')
            ->orderBy('name_client')
            ->get(['id_client', 'name_client']);

        // Contadores para la vista
        $activeCount = Client::whereNull('deleted_at')->count();
        $trashedCount = Client::onlyTrashed()->count();
        $totalCount = Client::withTrashed()->count();

        return view('admin.clients.index', compact(
            'clients',
            'cities',
            'allClientsForExport',
            'activeCount',
            'trashedCount',
            'totalCount',
            'filter'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_client'                   => 'required|string|max:120',
            'business_name'                 => 'nullable|string|max:150',
            'tax_code'                      => 'nullable|string|max:20',
            'type_client'                   => 'nullable|string|in:cliente,prospecto',
            'general_phone'                 => 'nullable|string|max:20',
            'general_email'                 => 'nullable|email|max:120',
            'id_cities'                     => 'nullable',
            'address'                       => 'nullable|string|max:255',
            'contacts.*.name'               => 'required|string|max:100',
            'contacts.*.last_names'         => 'nullable|string|max:100',
            'contacts.*.qualification'      => 'nullable|string|max:30',
            'contacts.*.email'              => 'nullable|email|max:80',
            'contacts.*.first_phone'        => 'nullable|string|max:20',
            'contacts.*.second_phone'       => 'nullable|string|max:20',
        ]);

        $client = Client::create([
            'name_client'   => $request->name_client,
            'business_name' => $request->business_name,
            'tax_code'      => $request->tax_code,
            'type_client'   => $request->type_client,
            'general_phone' => $request->general_phone,
            'general_email' => $request->general_email,
            'id_cities'     => $request->id_cities,
            'address'       => $request->address,
        ]);

        foreach ($request->input('contacts', []) as $i => $data) {
            if (empty($data['name'])) continue;
            $client->contacts()->create([
                'name'          => $data['name'],
                'last_names'    => $data['last_names']    ?? null,
                'qualification' => $data['qualification'] ?? null,
                'email'         => $data['email']         ?? null,
                'first_phone'   => $data['first_phone']   ?? null,
                'second_phone'  => $data['second_phone']  ?? null,
                'es_principal'  => $i === 0,
            ]);
        }

        return redirect()->route('admin.clients.index')
            ->with('success', 'Cliente creado correctamente.');
    }

    public function create()
    {
        return view('admin.clients.create');
    }

    /**
     * Editar un cliente (permite editar incluso si está eliminado)
     * ✅ Recibe el modelo Client gracias al Route Model Binding
     */
    public function edit(Client $client)
    {
        $client->load(['contacts' => fn($q) => $q->orderBy('es_principal', 'desc')->orderBy('created_at')]);
        return view('admin.clients.edit', compact('client'));
    }

    /**
     * Actualizar un cliente (permite actualizar incluso si está eliminado)
     * ✅ Recibe el modelo Client gracias al Route Model Binding
     */
    public function update(Request $request, Client $client)
    {
        $request->validate([
            'name_client'                     => 'required|string|max:120',
            'business_name'                   => 'nullable|string|max:150',
            'tax_code'                        => 'nullable|string|max:20',
            'type_client'                     => 'nullable|string|in:cliente,prospecto',
            'general_phone'                   => 'nullable|string|max:20',
            'general_email'                   => 'nullable|email|max:120',
            'id_cities'                       => 'nullable|exists:cities,id_cities',
            'address'                         => 'nullable|string|max:255',
            'contacts.*.id'                   => 'nullable|integer',
            'contacts.*.name'                 => 'required|string|max:100',
            'contacts.*.last_names'           => 'nullable|string|max:100',
            'contacts.*.qualification'        => 'nullable|string|max:30',
            'contacts.*.email'                => 'nullable|email|max:80',
            'contacts.*.first_phone'          => 'nullable|string|max:20',
            'contacts.*.second_phone'         => 'nullable|string|max:20',
            'contacts.*.es_principal'         => 'nullable|boolean',
            'new_contacts.*.name'             => 'nullable|string|max:100',
            'new_contacts.*.last_names'       => 'nullable|string|max:100',
            'new_contacts.*.qualification'    => 'nullable|string|max:30',
            'new_contacts.*.email'            => 'nullable|email|max:80',
            'new_contacts.*.first_phone'      => 'nullable|string|max:20',
            'new_contacts.*.second_phone'     => 'nullable|string|max:20',
            'delete_contacts'                 => 'nullable|array',
            'delete_contacts.*'               => 'integer|exists:contacts,id_contacts',
        ]);

        // Actualizar datos del cliente
        $client->update([
            'name_client'   => $request->name_client,
            'business_name' => $request->business_name,
            'tax_code'      => $request->tax_code,
            'type_client'   => $request->type_client,
            'general_phone' => $request->general_phone,
            'general_email' => $request->general_email,
            'id_cities'     => $request->id_cities,
            'address'       => $request->address,
        ]);

        // Eliminar contactos marcados (soft delete)
        if ($request->filled('delete_contacts')) {
            Contact::whereIn('id_contacts', $request->delete_contacts)
                ->where('id_client', $client->id_client)
                ->delete();
        }

        // Actualizar contactos existentes
        foreach ($request->input('contacts', []) as $data) {
            if (empty($data['id'])) continue;
            $contact = Contact::where('id_contacts', $data['id'])
                ->where('id_client', $client->id_client)
                ->first();
            if (!$contact) continue;
            $contact->update([
                'name'          => $data['name'],
                'last_names'    => $data['last_names']    ?? null,
                'qualification' => $data['qualification'] ?? null,
                'email'         => $data['email']         ?? null,
                'first_phone'   => $data['first_phone']   ?? null,
                'second_phone'  => $data['second_phone']  ?? null,
                'es_principal'  => isset($data['es_principal']),
            ]);
        }

        // Agregar nuevos contactos
        foreach ($request->input('new_contacts', []) as $data) {
            if (empty($data['name'])) continue;
            $client->contacts()->create([
                'name'          => $data['name'],
                'last_names'    => $data['last_names']    ?? null,
                'qualification' => $data['qualification'] ?? null,
                'email'         => $data['email']         ?? null,
                'first_phone'   => $data['first_phone']   ?? null,
                'second_phone'  => $data['second_phone']  ?? null,
                'es_principal'  => false,
            ]);
        }

        return redirect()->route('admin.clients.index')
            ->with('success', 'Cliente actualizado correctamente.');
    }

    /**
     * Eliminar un cliente (soft delete o force delete)
     * ✅ Recibe el modelo Client gracias al Route Model Binding
     */
    public function destroy(Client $client)
    {
        if ($client->trashed()) {
            $client->forceDelete();
            return back()->with('success', 'Cliente eliminado permanentemente.');
        }

        $client->delete();
        return back()->with('success', 'Cliente eliminado correctamente.');
    }

    /**
     * Restaurar un cliente eliminado
     * ✅ Recibe el modelo Client gracias al Route Model Binding
     */
    public function restore(Client $client)
    {
        if ($client->trashed()) {
            $client->restore();
            return back()->with('success', 'Cliente restaurado correctamente.');
        }

        return back()->with('warning', 'El cliente ya está activo.');
    }

    /**
     * Eliminar permanentemente un cliente
     * ✅ Recibe el modelo Client gracias al Route Model Binding
     */
    public function forceDestroy(Client $client)
    {
        $client->forceDelete();
        return back()->with('success', 'Cliente eliminado permanentemente.');
    }

    /**
     * Eliminar múltiples clientes (activos → soft delete, eliminados → force delete)
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'integer|exists:clients,id_client',
        ]);

        $ids = $request->ids;

        // Obtener clientes activos y eliminados por separado
        $activeClients = Client::whereIn('id_client', $ids)
            ->whereNull('deleted_at')
            ->get();

        $trashedClients = Client::withTrashed()
            ->whereIn('id_client', $ids)
            ->whereNotNull('deleted_at')
            ->get();

        $activeCount = $activeClients->count();
        $trashedCount = $trashedClients->count();
        $totalCount = $activeCount + $trashedCount;

        if ($totalCount === 0) {
            return redirect()->route('admin.clients.index')
                ->with('warning', 'No se encontraron clientes para eliminar.');
        }

        // Soft delete a los activos
        foreach ($activeClients as $client) {
            $client->delete();
        }

        // Force delete a los ya eliminados
        foreach ($trashedClients as $client) {
            $client->forceDelete();
        }

        $message = "{$activeCount} cliente(s) eliminado(s) correctamente.";
        if ($trashedCount > 0) {
            $message .= " {$trashedCount} cliente(s) eliminado(s) permanentemente.";
        }

        return redirect()->route('admin.clients.index')
            ->with('success', $message);
    }

    /**
     * Restaurar múltiples clientes eliminados
     */
    public function bulkRestore(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'integer|exists:clients,id_client',
        ]);

        $count = Client::withTrashed()
            ->whereIn('id_client', $request->ids)
            ->whereNotNull('deleted_at')
            ->restore();

        if ($count === 0) {
            return redirect()->route('admin.clients.index')
                ->with('warning', 'No se encontraron clientes eliminados para restaurar.');
        }

        return redirect()->route('admin.clients.index')
            ->with('success', "{$count} cliente(s) restaurado(s) correctamente.");
    }

    /**
     * Eliminar permanentemente múltiples clientes
     */
    public function bulkForceDestroy(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'integer|exists:clients,id_client',
        ]);

        $count = Client::withTrashed()
            ->whereIn('id_client', $request->ids)
            ->forceDelete();

        if ($count === 0) {
            return redirect()->route('admin.clients.index')
                ->with('warning', 'No se encontraron clientes para eliminar permanentemente.');
        }

        return redirect()->route('admin.clients.index')
            ->with('success', "{$count} cliente(s) eliminado(s) permanentemente.");
    }

    // ── VISTA IMPORTAR ────────────────────────────────────────
    public function importView()
    {
        return view('admin.clients.import');
    }

    // ── PROCESAR IMPORTACIÓN ──────────────────────────────────
    public function import(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ], [
            'archivo.required' => 'Debes seleccionar un archivo.',
            'archivo.mimes'    => 'Solo se aceptan archivos .xlsx, .xls o .csv.',
            'archivo.max'      => 'El archivo no puede superar los 5MB.',
        ]);

        $import = new ClientsImport();

        try {
            Excel::import($import, $request->file('archivo'));
        } catch (\Exception $e) {
            return back()->withErrors(['archivo' => 'Error al procesar el archivo: ' . $e->getMessage()]);
        }

        $msg = "Importación completada: {$import->imported} cliente(s) procesado(s).";
        if ($import->skipped > 0) $msg .= " {$import->skipped} fila(s) omitida(s).";
        if (!empty($import->errors)) $msg .= ' Con errores: ' . implode(' | ', $import->errors);

        return redirect()->route('admin.clients.index')->with('success', $msg);
    }

    // ── EXPORTAR EXCEL CON OPCIONES ──────────────────────────
    public function exportExcel(Request $request)
    {
        // Si se envía un ID específico, usar el export por ID
        if ($request->has('client_id') && !empty($request->client_id)) {
            $export = new ClientsExportById($request->client_id);
            $filename = 'cliente_id_' . $request->client_id . '_' . now()->format('Ymd') . '.xlsx';
        } else {
            $export = new ClientsExport();
            $filename = 'clientes_' . now()->format('Ymd') . '.xlsx';
        }

        return Excel::download($export, $filename);
    }

    // ── EXPORTAR PDF CON OPCIONES ─────────────────────────────
    public function exportPdf(Request $request)
    {
        $query = Client::with(['contacts' => function($q) {
            $q->orderBy('es_principal', 'desc')->orderBy('created_at');
        }])
        ->withCount('contacts')
        ->whereNull('deleted_at'); // Solo clientes activos para exportar

        // Si se envía un ID específico
        if ($request->has('client_id') && !empty($request->client_id)) {
            $query->where('id_client', $request->client_id);
            $suffix = '_id_' . $request->client_id;
        } else {
            $suffix = '';
        }

        $clients = $query->orderBy('name_client')->get();

        $pdf = Pdf::loadView('admin.clients.export-pdf', compact('clients'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont'          => 'Arial',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => true,
                'dpi'                  => 96,
                'isPhpEnabled'         => true,
                'encoding'             => 'UTF-8',
            ]);

        return $pdf->download('clientes' . $suffix . '_' . now()->format('Ymd') . '.pdf');
    }

    // ── OBTENER CLIENTE POR ID (PARA EL MODAL) ──────────────
    public function getClient(Request $request)
    {
        $client = Client::with(['contacts' => function($q) {
            $q->orderBy('es_principal', 'desc')->orderBy('created_at');
        }])
        ->withCount('contacts')
        ->find($request->id);

        if (!$client) {
            return response()->json(['error' => 'Cliente no encontrado'], 404);
        }

        return response()->json($client);
    }
}
