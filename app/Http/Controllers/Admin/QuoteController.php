<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Contact;
use App\Models\DetailQuote;
use App\Models\Labels;
use App\Models\Quote;
use App\Models\QuoteAccommodation;
use App\Models\QuoteDay;
use App\Models\Season;
use App\Models\Service;
use App\Models\Supplier;
use App\Models\Tariff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class QuoteController extends Controller
{
    public function index(Request $request)
    {
        $query = Quote::with(['client', 'contact', 'user', 'quoteDays.details.service', 'quoteDays.details.supplier']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('quote_number', 'like', "%{$search}%")
                    ->orWhere('correlative', 'like', "%{$search}%")
                    ->orWhereHas('client', function ($sub) use ($search) {
                        $sub->where('name_client', 'like', "%{$search}%");
                    });
            });
        }

        $quotes = $query->orderBy('created_at', 'desc')->paginate(15);

        $clients = Client::orderBy('name_client')->get();
        $contacts = Contact::orderBy('name')->get();
        $users = User::orderBy('name')->get();
        $services = Service::with(['labels', 'supplier'])->where('status', 'active')->get();
        $labels = Labels::where('status', 'active')->get();
        $suppliers = Supplier::whereNull('deleted_at')->orderBy('supplier_name')->get();

        $totalQuotes = Quote::count();
        $statusCounts = [
            'draft' => Quote::where('status', 'draft')->count(),
            'sent' => Quote::where('status', 'sent')->count(),
            'approved' => Quote::where('status', 'approved')->count(),
            'rejected' => Quote::where('status', 'rejected')->count(),
            'expired' => Quote::where('status', 'expired')->count(),
            'cancelled' => Quote::where('status', 'cancelled')->count(),
        ];

        return view('admin.quote.index', compact(
            'quotes',
            'clients',
            'contacts',
            'users',
            'services',
            'labels',
            'suppliers',
            'totalQuotes',
            'statusCounts'
        ));
    }

    public function create()
    {
        $clients = Client::orderBy('name_client')->get();
        $contacts = Contact::orderBy('name')->get();
        $users = User::orderBy('name')->get();

        $services = Service::with(['labels', 'supplier', 'tariffs'])
            ->where('status', 'active')
            ->whereHas('category', function ($q) {
                $q->where('is_accommodation', false);
            })
            ->get();

        $accommodationServices = $this->accommodationServicesQuery(
            Service::with(['labels', 'supplier', 'tariffs'])
        )->get();

        $labels = Labels::where('status', 'active')->get();
        $suppliers = Supplier::whereNull('deleted_at')->orderBy('supplier_name')->get();

        $lastQuote = Quote::orderBy('id_quote', 'desc')->first();
        $nextNumber = $lastQuote ? intval(substr($lastQuote->quote_number, -4)) + 1 : 1;
        $quoteNumber = 'COT-'.str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        return view('admin.quote.create', compact(
            'clients',
            'contacts',
            'users',
            'services',
            'accommodationServices',
            'labels',
            'suppliers',
            'quoteNumber'
        ));
    }

    public function store(Request $request)
    {
        try {
            \Log::info('📝 Datos recibidos:', $request->all());

            $dateMode = $request->input('date_mode', 'dates');

            $rules = [
                'name' => 'nullable|string|max:300',
                'id_users' => 'nullable|exists:users,id',
                'id_client' => 'nullable|exists:clients,id_client',
                'id_contacts' => 'nullable|exists:contacts,id_contacts',
                'passengers_count' => 'nullable|integer|min:1',
                'notes' => 'nullable|string',
                'date_mode' => 'nullable|in:dates,days',
            ];

            if ($dateMode === 'days') {
                $rules['days_count'] = 'required|integer|min:1|max:60';
            } else {
                $rules['start_date'] = 'required|date';
                $rules['end_date'] = 'required|date|after_or_equal:start_date';
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                \Log::error('❌ Validación falló:', $validator->errors()->toArray());

                return redirect()->back()->withErrors($validator)->withInput();
            }

            \Log::info('✅ Validación pasó');

            DB::beginTransaction();

            $lastQuote = Quote::orderBy('id_quote', 'desc')->first();
            $nextNumber = $lastQuote ? intval(substr($lastQuote->quote_number, -4)) + 1 : 1;
            $quoteNumber = 'COT-'.str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            \Log::info('Creando cotización: '.$quoteNumber);

            $quote = Quote::create([
                'name' => $request->name ?? 'Cotización '.$quoteNumber,
                'quote_number' => $quoteNumber,
                'correlative' => null,
                'correlative_assigned_at' => null,
                'id_users' => $request->id_users ?? auth()->id(),
                'id_client' => $request->id_client,
                'id_contacts' => $request->id_contacts,
                'status' => 'draft',
                'days' => $dateMode === 'days' ? $request->days_count : null,
                'start_date' => $dateMode === 'dates' ? $request->start_date : null,
                'end_date' => $dateMode === 'dates' ? $request->end_date : null,
                'passengers_count' => $request->passengers_count,
                'notes' => $request->notes,
                'subtotal' => 0,
                'total' => 0,
            ]);

            \Log::info('Cotización creada ID: '.$quote->id_quote);

            if (! method_exists($quote, 'generateItineraryDays')) {
                throw new \Exception('El método generateItineraryDays() no existe en el modelo Quote');
            }

            if ($dateMode === 'days') {
                $quote->generateItineraryDays();
            } else {
                $quote->generateItineraryDays();
            }
            \Log::info('✅ Días generados');

            if (! method_exists($quote, 'calculateTotals')) {
                throw new \Exception('El método calculateTotals() no existe en el modelo Quote');
            }

            $quote->calculateTotals(1);
            \Log::info('✅ Totales calculados');

            DB::commit();

            return redirect()
                ->route('admin.quotes.show', $quote->id_quote)
                ->with('success', '✅ Cotización creada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('❌ ERROR: '.$e->getMessage());
            \Log::error('📄 Archivo: '.$e->getFile().' Línea: '.$e->getLine());
            \Log::error('📚 Trace: '.$e->getTraceAsString());

            return redirect()
                ->back()
                ->with('error', '❌ Error: '.$e->getMessage())
                ->withInput();
        }
    }

    public function show(Quote $quote)
    {
        $quote->load([
            'client',
            'contact',
            'user',
            'quoteDays.details.service.labels',
            'quoteDays.details.service.supplier',
            'quoteDays.details.tariff',
            'quoteDays.details.supplier',
            'accommodations.service',
            'accommodations.supplier',
            'accommodations.tariff',
            'accommodations.quoteDay',
        ]);

        $totals = $quote->getTotalsByOption();

        $hotelsByDayOption1 = $quote->accommodationOption1()
            ->with('quoteDay')
            ->get()
            ->keyBy('id_quote_day');

        $hotelsByDayOption2 = $quote->accommodationOption2()
            ->with('quoteDay')
            ->get()
            ->keyBy('id_quote_day');

        return view('admin.quote.show', compact(
            'quote',
            'totals',
            'hotelsByDayOption1',
            'hotelsByDayOption2'
        ));
    }

    public function edit(Quote $quote)
    {
        $clients = Client::orderBy('name_client')->get();
        $contacts = Contact::orderBy('name')->get();
        $users = User::orderBy('name')->get();

        $services = Service::with([
            'labels',
            'supplier',
            'category',
            'tariffs' => function ($query) {
                $query->whereNull('id_season')
                    ->where('status', 'active')
                    ->with('subCategory');
            },
        ])
            ->where('status', 'active')
            ->get();

        $accommodationServices = $this->accommodationServicesQuery(
            Service::with(['labels', 'supplier', 'tariffs', 'category'])
        )->get();

        $labels = Labels::where('status', 'active')->get();
        $suppliers = Supplier::whereNull('deleted_at')->orderBy('supplier_name')->get();

        $quote->load([
            'quoteDays.details.service.tariffs' => function ($query) {
                $query->whereNull('id_season')
                    ->where('status', 'active')
                    ->with('subCategory');
            },
            'quoteDays.details.supplier',
            'quoteDays.details.tariff.subCategory',
            'accommodations.service',
            'accommodations.quoteDay',
        ]);

        return view('admin.quote.edit', compact(
            'quote',
            'clients',
            'contacts',
            'users',
            'services',
            'accommodationServices',
            'labels',
            'suppliers'
        ));
    }

    public function update(Request $request, Quote $quote)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:300',
            'id_users' => 'nullable|exists:users,id',
            'id_client' => 'nullable|exists:clients,id_client',
            'id_contacts' => 'nullable|exists:contacts,id_contacts',
            'status' => 'nullable|in:draft,sent,approved,rejected,expired,cancelled',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'expiration_date' => 'nullable|date|after_or_equal:start_date',
            'passengers_count' => 'nullable|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $oldStatus = $quote->status;
            $newStatus = $request->status ?? $quote->status;
            $datesChanged = $request->start_date !== $quote->start_date?->format('Y-m-d')
                || $request->end_date !== $quote->end_date?->format('Y-m-d');

            $quote->update([
                'name' => $request->name,
                'id_users' => $request->id_users ?? auth()->id(),
                'id_client' => $request->id_client,
                'id_contacts' => $request->id_contacts,
                'status' => $newStatus,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'expiration_date' => $request->expiration_date,
                'passengers_count' => $request->passengers_count,
                'notes' => $request->notes,
            ]);

            if ($datesChanged && $request->start_date && $request->end_date) {
                $quote->generateItineraryDays();
            }

            $correlativeAssigned = false;
            if ($newStatus === 'approved' && $oldStatus !== 'approved') {
                if (! $quote->start_date) {
                    throw new \Exception('La cotización debe tener una fecha de inicio para asignar el correlativo.');
                }
                $correlativeAssigned = $quote->assignCorrelative();
            }

            $quote->calculateTotals(1);

            DB::commit();

            $message = 'Cotización actualizada exitosamente';
            if ($datesChanged) {
                $message .= ' Las fechas del itinerario fueron actualizadas y los servicios existentes se conservaron.';
            }
            if ($correlativeAssigned && $quote->correlative) {
                $message .= ' - Correlativo asignado: '.$quote->correlative;
            }

            return redirect()
                ->route('admin.quotes.show', $quote->id_quote)
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar cotización: '.$e->getMessage(), [
                'quote_id' => $quote->id_quote,
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()
                ->back()
                ->with('error', 'Error al actualizar la cotización: '.$e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Quote $quote)
    {
        try {
            $quote->delete();

            return redirect()
                ->route('admin.quotes.index')
                ->with('success', 'Cotización eliminada exitosamente');
        } catch (\Exception $e) {
            Log::error('Error al eliminar cotización: '.$e->getMessage(), [
                'quote_id' => $quote->id_quote,
            ]);

            return redirect()
                ->back()
                ->with('error', 'Error al eliminar la cotización: '.$e->getMessage());
        }
    }

    public function getContactsByClient($clientId)
    {
        $contacts = Contact::where('id_client', $clientId)
            ->orderBy('name')
            ->get(['id_contacts as id', 'name', 'last_names']);

        return response()->json([
            'success' => true,
            'data' => $contacts,
        ]);
    }

    private function accommodationServicesQuery($query)
    {
        $terms = ['hotel', 'hospedaje', 'hospedaje', 'room', 'habitacion', 'habitación', 'alojamiento', 'lodging', 'resort', 'hostel', 'suite'];

        return $query->where('status', 'active')->where(function ($query) use ($terms) {
            $query->whereHas('category', function ($subQuery) use ($terms) {
                $subQuery->where('is_accommodation', true)
                    ->orWhere(function ($nameQuery) use ($terms) {
                        foreach ($terms as $term) {
                            $nameQuery->orWhereRaw('LOWER(name) LIKE ?', ['%'.$term.'%']);
                        }
                    });
            })->orWhere(function ($fallback) use ($terms) {
                foreach ($terms as $term) {
                    $fallback->orWhereRaw('LOWER(name_service) LIKE ?', ['%'.$term.'%']);
                }
            });
        });
    }

    private function isAccommodationService(Service $service): bool
    {
        if ($service->is_accommodation) {
            return true;
        }

        $keywords = ['hotel', 'hospedaje', 'room', 'habitacion', 'habitación', 'alojamiento', 'lodging', 'resort', 'hostel', 'suite'];
        $serviceName = strtolower((string) $service->name_service);
        $categoryName = strtolower((string) ($service->category?->name ?? ''));

        foreach ($keywords as $keyword) {
            if (str_contains($serviceName, $keyword) || str_contains($categoryName, $keyword)) {
                return true;
            }
        }

        return false;
    }

    private function getTariffForService(Service $service, int $passengersCount, ?Quote $quote = null): ?Tariff
    {
        $query = Tariff::with('season')
            ->where('id_service', $service->id_service)
            ->where('status', 'active');

        $query->where(function ($query) use ($passengersCount) {
            $query->where('pricing_type', 'flat')
                ->orWhere(function ($query) use ($passengersCount) {
                    $query->where(function ($minimum) use ($passengersCount) {
                        $minimum->where('min_people_count', '<=', $passengersCount)
                            ->orWhereNull('min_people_count');
                    })
                        ->where(function ($range) use ($passengersCount) {
                            $range->where('max_people_count', '>=', $passengersCount)
                                ->orWhereNull('max_people_count');
                        });
                });
        });
        if ($quote?->start_date && $quote->end_date) {
            $query->where(function ($query) use ($quote) {
                $query->whereNull('id_season')
                    ->orWhereHas('season', function ($season) use ($quote) {
                        $season->where('status', 'active')
                            ->where('start_date', '<=', $quote->end_date)
                            ->where('end_date', '>=', $quote->start_date);
                    });
            });

            return $query
                ->orderByRaw('CASE WHEN id_season IS NULL THEN 0 ELSE 1 END DESC')
                ->orderByDesc('price')
                ->first();
        }

        return $query->orderByDesc('price')->first();
    }

    public function duplicate(Quote $quote)
    {
        try {
            DB::beginTransaction();

            $lastQuote = Quote::orderBy('id_quote', 'desc')->first();
            $nextNumber = $lastQuote ? intval(substr($lastQuote->quote_number, -4)) + 1 : 1;
            $quoteNumber = 'COT-'.str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            $newQuote = $quote->replicate();
            $newQuote->quote_number = $quoteNumber;
            $newQuote->name = $quote->name.' - Copia';
            $newQuote->status = 'draft';
            $newQuote->correlative = null;
            $newQuote->correlative_assigned_at = null;
            $newQuote->subtotal = 0;
            $newQuote->total = 0;
            $newQuote->created_at = now();
            $newQuote->id_users = auth()->id();
            $newQuote->save();

            $quote->load('quoteDays.details', 'accommodations');

            $dayIdMap = [];

            foreach ($quote->quoteDays as $oldDay) {
                $newDay = QuoteDay::create([
                    'id_quote' => $newQuote->id_quote,
                    'day_number' => $oldDay->day_number,
                    'date' => $oldDay->date,
                ]);
                $dayIdMap[$oldDay->id_quote_day] = $newDay->id_quote_day;

                foreach ($oldDay->details as $oldDetail) {
                    $newDetail = $oldDetail->replicate();
                    $newDetail->id_quote_day = $newDay->id_quote_day;
                    $newDetail->save();
                }
            }

            foreach ($quote->accommodations as $oldAcc) {
                $newAcc = $oldAcc->replicate();
                $newAcc->id_quote = $newQuote->id_quote;
                $newAcc->id_quote_day = $dayIdMap[$oldAcc->id_quote_day] ?? null;
                $newAcc->save();
            }

            $newQuote->calculateTotals(1);

            DB::commit();

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cotización duplicada exitosamente',
                    'quote_id' => $newQuote->id_quote,
                    'redirect' => route('admin.quotes.show', $newQuote->id_quote),
                ]);
            }

            return redirect()
                ->route('admin.quotes.show', $newQuote->id_quote)
                ->with('success', 'Cotización duplicada exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al duplicar cotización: '.$e->getMessage(), [
                'original_quote_id' => $quote->id_quote,
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString(),
            ]);

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al duplicar la cotización: '.$e->getMessage(),
                ], 500);
            }

            return redirect()
                ->back()
                ->with('error', 'Error al duplicar la cotización: '.$e->getMessage());
        }
    }

    public function getSuppliers()
    {
        $suppliers = Supplier::whereNull('deleted_at')
            ->orderBy('supplier_name')
            ->get(['id_supplier', 'supplier_name']);

        return response()->json([
            'success' => true,
            'data' => $suppliers,
        ]);
    }

    public function getServicesBySupplier($supplierId)
    {
        $services = Service::with(['labels', 'supplier'])
            ->where('id_supplier', $supplierId)
            ->where('status', 'active')
            ->get(['id_service', 'name_service', 'id_labels', 'id_supplier']);

        return response()->json([
            'success' => true,
            'data' => $services,
        ]);
    }

    public function getServicesByLabel($labelId)
    {
        $services = Service::with(['labels', 'supplier'])
            ->where('id_labels', $labelId)
            ->where('status', 'active')
            ->get(['id_service', 'name_service', 'id_labels', 'id_supplier']);

        return response()->json([
            'success' => true,
            'data' => $services,
        ]);
    }

    public function getTariffsByService($serviceId)
    {
        try {
            $service = Service::find($serviceId);

            if (! $service) {
                return response()->json([
                    'success' => false,
                    'message' => 'Servicio no encontrado',
                ], 404);
            }

            $tariffs = Tariff::where('id_service', $serviceId)
                ->where('status', 'active')
                ->orderBy('id_season')
                ->orderBy('min_people_count')
                ->get(['id_tariff', 'price', 'min_people_count', 'max_people_count', 'id_season']);

            $seasonIds = $tariffs->pluck('id_season')->filter()->unique();
            $seasons = [];
            if ($seasonIds->count() > 0) {
                $seasons = Season::whereIn('id_season', $seasonIds)
                    ->where('status', 'active')
                    ->pluck('name', 'id_season')
                    ->toArray();
            }

            return response()->json([
                'success' => true,
                'pricing_type' => $service->pricing_type,
                'is_accommodation' => $this->isAccommodationService($service),
                'data' => $tariffs,
                'seasons' => $seasons,
            ]);

        } catch (\Exception $e) {
            Log::error('Error en getTariffsByService: '.$e->getMessage(), [
                'service_id' => $serviceId,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al cargar tarifas: '.$e->getMessage(),
            ], 500);
        }
    }

    public function getFilteredServices(Request $request)
    {
        $query = Service::with(['labels', 'supplier'])
            ->where('status', 'active');

        if ($request->filled('supplier_id')) {
            $query->where('id_supplier', $request->supplier_id);
        }

        if ($request->filled('label_id')) {
            $query->where('id_labels', $request->label_id);
        }

        if ($request->filled('exclude_accommodation')) {
            $query->whereHas('subCategory.category', fn ($q) => $q->where('is_accommodation', false));
        }

        $services = $query->get(['id_service', 'name_service', 'id_labels', 'id_supplier']);
        $services->load(['labels', 'supplier']);

        return response()->json([
            'success' => true,
            'data' => $services,
        ]);
    }

    public function addService(Request $request, Quote $quote)
    {
        $validator = Validator::make($request->all(), [
            'day_number' => 'required|integer|min:1',
            'id_service' => 'required|exists:service,id_service',
            'id_tariff' => 'nullable|exists:tariff,id_tariff',
            'quantity' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $day = $quote->quoteDays()->where('day_number', $request->integer('day_number'))->first();
            $service = Service::where('status', 'active')->find($request->integer('id_service'));

            if (! $day || ! $service) {
                return response()->json([
                    'success' => false,
                    'message' => 'El día o servicio seleccionado no está disponible.',
                ], 422);
            }

            $tariff = $request->filled('id_tariff')
                ? Tariff::where('id_service', $service->id_service)
                    ->whereNull('id_season')
                    ->where('status', 'active')
                    ->find($request->integer('id_tariff'))
                : $this->getTariffForService($service, $quote->passengers_count ?: 1, $quote);

            if (! $tariff) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay una tarifa activa disponible para este servicio.',
                ], 422);
            }

            $quantity = $request->integer('quantity', $quote->passengers_count ?: 1);
            $unitPrice = $this->resolveTariffPrice($tariff, $quantity);
            $detail = DetailQuote::create([
                'id_quote_day' => $day->id_quote_day,
                'id_service' => $service->id_service,
                'id_tariff' => $tariff->id_tariff,
                'id_supplier' => $service->id_supplier,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'subtotal' => $unitPrice * $quantity,
            ]);

            $quote->calculateTotals(1);

            return response()->json([
                'success' => true,
                'message' => 'Servicio agregado correctamente.',
                'detail_id' => $detail->id_detail_quote,
            ]);
        } catch (\Throwable $e) {
            Log::error('Error al agregar servicio a cotización: '.$e->getMessage(), [
                'quote_id' => $quote->id_quote,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'No se pudo agregar el servicio.',
            ], 500);
        }
    }

    public function removeService(Quote $quote, DetailQuote $detail)
    {
        if (! $quote->details()->where('id_detail_quote', $detail->id_detail_quote)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'El servicio no pertenece a esta cotización.',
            ], 404);
        }

        $detail->delete();
        $quote->calculateTotals(1);

        return response()->json([
            'success' => true,
            'message' => 'Servicio eliminado correctamente.',
        ]);
    }

    public function updateServiceDetail(Request $request, Quote $quote, DetailQuote $detail)
    {
        if (! $quote->details()->where('id_detail_quote', $detail->id_detail_quote)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'El servicio no pertenece a esta cotización.',
            ], 404);
        }

        $data = $request->validate([
            'id_tariff' => 'required|exists:tariff,id_tariff',
            'unit_price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
        ]);

        $tariff = Tariff::where('id_tariff', $data['id_tariff'])
            ->where('id_service', $detail->id_service)
            ->whereNull('id_season')
            ->where('status', 'active')
            ->firstOrFail();

        $unitPrice = $this->resolveTariffPrice($tariff, $data['quantity']);

        $detail->update([
            'id_tariff' => $tariff->id_tariff,
            'unit_price' => $unitPrice,
            'quantity' => $data['quantity'],
            'subtotal' => $unitPrice * $data['quantity'],
        ]);

        $quote->calculateTotals(1);

        return response()->json([
            'success' => true,
            'message' => 'Servicio actualizado correctamente.',
            'unit_price' => number_format((float) $detail->unit_price, 2, '.', ''),
            'subtotal' => number_format((float) $detail->subtotal, 2, '.', ''),
        ]);
    }

    private function resolveTariffPrice(Tariff $tariff, int $passengersCount): float
    {
        if ($tariff->pricing_type === 'flat') {
            return (float) ($tariff->price ?? 0);
        }

        $matchingTariff = Tariff::where('id_service', $tariff->id_service)
            ->where('id_subcategories', $tariff->id_subcategories)
            ->whereNull('id_season')
            ->where('status', 'active')
            ->where(function ($query) use ($passengersCount) {
                $query->whereNull('min_people_count')
                    ->orWhere('min_people_count', '<=', $passengersCount);
            })
            ->where(function ($query) use ($passengersCount) {
                $query->whereNull('max_people_count')
                    ->orWhere('max_people_count', '>=', $passengersCount);
            })
            ->first();

        return (float) ($matchingTariff?->price ?? 0);
    }

    public function changeStatus(Request $request, Quote $quote)
    {
        $status = $request->validate([
            'status' => 'required|in:draft,sent,approved,rejected,expired,cancelled',
        ])['status'];

        $oldStatus = $quote->status;
        $quote->status = $status;
        $quote->save();

        $correlative = null;
        if ($status === 'approved' && $oldStatus !== 'approved') {
            if (! $quote->start_date) {
                return response()->json([
                    'success' => false,
                    'message' => 'La cotización debe tener una fecha de inicio para asignar el correlativo.',
                ], 422);
            }

            $assigned = $quote->assignCorrelative();
            $correlative = $quote->fresh()->correlative;

            if (! $assigned || ! $correlative) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo generar el correlativo para la cotización aprobada.',
                ], 422);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Estado actualizado correctamente.',
            'correlative' => $correlative,
        ]);
    }

    public function addAccommodationToDay(Request $request, Quote $quote)
    {
        $data = $request->validate([
            'option_number' => 'required|integer|in:1,2',
            'day_number' => 'nullable|integer|min:1',
            'day_start' => 'nullable|integer|min:1',
            'day_end' => 'nullable|integer|min:1|gte:day_start',
            'id_service' => 'required|exists:service,id_service',
            'id_tariff' => 'nullable|exists:tariff,id_tariff',
        ]);

        $startDay = (int) ($request->input('day_start', $request->input('day_number', 1)));
        $endDay = (int) ($request->input('day_end', $request->input('day_number', $startDay)));

        if ($endDay < $startDay) {
            return response()->json([
                'success' => false,
                'message' => 'El día final no puede ser menor que el día inicial.',
            ], 422);
        }

        $service = Service::with('category')->where('status', 'active')->find($data['id_service']);
        if (! $service || ! $this->isAccommodationService($service)) {
            return response()->json([
                'success' => false,
                'message' => 'El hotel seleccionado no está disponible.',
            ], 422);
        }

        $tariff = ! empty($data['id_tariff'])
            ? Tariff::where('id_service', $service->id_service)
                ->where('status', 'active')
                ->find($data['id_tariff'])
            : $this->getTariffForService($service, $quote->passengers_count ?: 1, $quote);

        if (! $tariff) {
            return response()->json([
                'success' => false,
                'message' => 'No hay una tarifa activa disponible para este hotel.',
            ], 422);
        }

        $daysAssigned = [];
        for ($dayNumber = $startDay; $dayNumber <= $endDay; $dayNumber++) {
            $day = $quote->quoteDays()->where('day_number', $dayNumber)->first();

            if (! $day) {
                continue;
            }

            $accommodation = QuoteAccommodation::updateOrCreate(
                [
                    'id_quote' => $quote->id_quote,
                    'option_number' => $data['option_number'],
                    'id_quote_day' => $day->id_quote_day,
                ],
                [
                    'id_service' => $service->id_service,
                    'id_tariff' => $tariff->id_tariff,
                    'id_supplier' => $service->id_supplier,
                    'unit_price' => $tariff->price,
                    'subtotal' => $tariff->price,
                ]
            );

            $daysAssigned[] = $accommodation->id_quote_accommodation;
        }

        if (empty($daysAssigned)) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró un día válido del itinerario para asignar el hotel.',
            ], 422);
        }

        $quote->calculateTotals(1);

        return response()->json([
            'success' => true,
            'message' => 'Hotel guardado correctamente.',
            'days_assigned' => count($daysAssigned),
        ]);
    }

    public function removeAccommodation(Quote $quote, QuoteAccommodation $accommodation)
    {
        if ($accommodation->id_quote !== $quote->id_quote) {
            return response()->json([
                'success' => false,
                'message' => 'El hotel no pertenece a esta cotización.',
            ], 404);
        }

        $accommodation->delete();
        $quote->calculateTotals(1);

        return response()->json([
            'success' => true,
            'message' => 'Hotel eliminado correctamente.',
        ]);
    }
}
