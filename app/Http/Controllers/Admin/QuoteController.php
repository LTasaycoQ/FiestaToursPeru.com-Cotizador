<?php

namespace App\Http\Controllers\Admin;

use App\Exports\QuoteExport;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Contact;
use App\Models\DetailQuote;
use App\Models\Labels;
use App\Models\Quote;
use App\Models\QuoteAccommodation;
use App\Models\QuoteDay;
use App\Models\QuotePassenger;
use App\Models\Season;
use App\Models\Service;
use App\Models\Supplier;
use App\Models\Tariff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

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
            Service::with([
                'labels',
                'supplier',
                'tariffs' => fn ($query) => $query
                    ->where('status', 'active')
                    ->where(function ($seasonQuery) {
                        $seasonQuery->whereNull('id_season')
                            ->orWhereHas('season', fn ($query) => $query->where('status', 'active'));
                    })
                    ->with('subCategory'),
            ])
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
                'id_labels' => 'required|exists:labels,id_labels',
                'days_count' => 'nullable|integer|min:1|max:60',
                'date_mode' => 'nullable|in:dates,days',
            ];

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
                'id_labels' => $request->id_labels,
                'status' => 'draft',
                'days' => $dateMode === 'days' ? $request->days_count : null,
                'start_date' => $dateMode === 'dates' ? $request->start_date : null,
                'end_date' => $dateMode === 'dates' ? $request->end_date : null,
                'passengers_count' => null,
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
            \Log::info('Totales calculados');

            DB::commit();

            return redirect()
                ->route('admin.quotes.edit', $quote->id_quote)
                ->with('success', '✅ Cotización creada. Ahora puedes registrar sus servicios.');

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
            ->whereHas('category', function ($query) {
                $query->where('pricing_type', 'tiered')
                    ->where('is_accommodation', false);
            })
            ->get();

        $accommodationServices = $this->accommodationServicesQuery(
            Service::with([
                'labels',
                'supplier',
                'category',
                'tariffs' => fn ($query) => $query
                    ->where('status', 'active')
                    ->where(function ($seasonQuery) {
                        $seasonQuery->whereNull('id_season')
                            ->orWhereHas('season', fn ($query) => $query->where('status', 'active'));
                    })
                    ->with('subCategory'),
            ])
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
            'market',
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

    public function exportExcel(Quote $quote)
    {
        return Excel::download(new QuoteExport($quote->id_quote), 'cotizacion-'.$quote->id_quote.'.xlsx');
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
            $oldPassengersCount = (int) ($quote->passengers_count ?: 1);
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

            $passengersCount = (int) ($request->passengers_count ?: 1);
            if ($oldPassengersCount !== $passengersCount) {
                $details = DetailQuote::whereHas('quoteDay', function ($query) use ($quote) {
                    $query->where('id_quote', $quote->id_quote);
                })->with('tariff')->get();

                foreach ($details as $detail) {
                    $tariff = $detail->tariff;
                    $selectedTariff = $tariff;

                    if ($tariff && $tariff->pricing_type !== 'flat') {
                        $selectedTariff = Tariff::where('id_service', $detail->id_service)
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
                            ->orderByDesc('price')
                            ->first();
                    }

                    if ($selectedTariff) {
                        $detail->id_tariff = $selectedTariff->id_tariff;
                        $detail->unit_price = (float) $selectedTariff->price;
                    }

                    $detail->quantity = $passengersCount;
                    $detail->subtotal = (float) $detail->unit_price * $passengersCount;
                    $detail->save();
                }
            }

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

    public function quote(Request $request, Quote $quote)
    {
        $data = $request->validate([
            'passengers_count' => 'required|integer|min:1',
            'pricing_type' => 'required|in:economico,vip,privado',
            'room_counts' => 'nullable|array',
            'room_counts.simple' => 'nullable|integer|min:0',
            'room_counts.doble' => 'nullable|integer|min:0',
            'room_counts.triple' => 'nullable|integer|min:0',
        ]);

        DB::transaction(function () use ($quote, $data): void {
            $passengersCount = (int) $data['passengers_count'];
            $subcategoryPattern = match ($data['pricing_type']) {
                'economico' => '%econom%',
                'vip' => '%vip%',
                'privado' => '%priv%',
            };

            foreach ($quote->details()->with('tariff')->get() as $detail) {
                $tariffQuery = Tariff::where('id_service', $detail->id_service)
                    ->where('status', 'active')
                    ->whereHas('subCategory', fn ($query) => $query->whereRaw('LOWER(name) LIKE ?', [$subcategoryPattern]));

                $tariff = (clone $tariffQuery)
                    ->where(function ($query) use ($passengersCount) {
                        $query->whereNull('min_people_count')->orWhere('min_people_count', '<=', $passengersCount);
                    })
                    ->where(function ($query) use ($passengersCount) {
                        $query->whereNull('max_people_count')->orWhere('max_people_count', '>=', $passengersCount);
                    })
                    ->orderByDesc('price')
                    ->first();

                // Keep the selected service type even when its passenger range is missing.
                $selectedTariff = $tariff ?: $tariffQuery
                    ->orderBy('min_people_count')
                    ->orderByDesc('price')
                    ->first();

                $detailData = [
                    'quantity' => $passengersCount,
                    'subtotal' => (float) $detail->unit_price * $passengersCount,
                ];

                if ($selectedTariff) {
                    $detailData['id_tariff'] = $selectedTariff->id_tariff;
                }

                if ($tariff) {
                    $detailData['unit_price'] = (float) $tariff->price;
                    $detailData['subtotal'] = (float) $tariff->price * $passengersCount;
                }

                $detail->update($detailData);
            }

            $roomCounts = collect($data['room_counts'] ?? [])
                ->mapWithKeys(fn ($count, $roomType) => [$roomType => (int) $count])
                ->only(['simple', 'doble', 'triple']);
            $totalCapacity = ($roomCounts['simple'] ?? 0)
                + (($roomCounts['doble'] ?? 0) * 2)
                + (($roomCounts['triple'] ?? 0) * 3);

            if ($roomCounts->sum() > 0 && $totalCapacity < $passengersCount) {
                throw ValidationException::withMessages([
                    'room_counts' => 'La capacidad de las habitaciones debe cubrir a todos los pasajeros.',
                ]);
            }

            if ($roomCounts->sum() > 0) {
                $accommodations = $quote->accommodations()
                    ->with(['service', 'quoteDay'])
                    ->get()
                    ->filter(fn ($accommodation) => $accommodation->service && $accommodation->quoteDay)
                    ->groupBy(fn ($accommodation) => $accommodation->option_number.':'.$accommodation->id_quote_day.':'.$accommodation->id_service);

                foreach ($accommodations as $accommodationGroup) {
                    $firstAccommodation = $accommodationGroup->first();
                    $service = $firstAccommodation->service;
                    $accommodationDate = $firstAccommodation->quoteDay?->date;
                    $tariffsByRoomType = [];

                    foreach ($roomCounts as $roomType => $roomCount) {
                        if ($roomCount < 1) {
                            continue;
                        }

                        $patterns = match ($roomType) {
                            'simple' => ['%spl%', '%single%', '%simple%'],
                            'doble' => ['%dbl%', '%double%', '%doble%', '%matrimonial%'],
                            'triple' => ['%tpl%', '%triple%'],
                        };

                        $tariffQuery = Tariff::where('id_service', $service->id_service)
                            ->where('status', 'active')
                            ->where(function ($seasonQuery) {
                                $seasonQuery->whereNull('id_season')
                                    ->orWhereHas('season', fn ($query) => $query->where('status', 'active'));
                            });

                        if ($accommodationDate) {
                            $tariffQuery->where(function ($query) use ($accommodationDate) {
                                $query
                                    ->whereNull('id_season')
                                    ->orWhereHas('season', function ($seasonQuery) use ($accommodationDate) {
                                        $seasonQuery
                                            ->where('status', 'active')
                                            ->whereDate('start_date', '<=', $accommodationDate)
                                            ->whereDate('end_date', '>=', $accommodationDate);
                                    });
                            });
                        }

                        $tariff = $tariffQuery
                            ->whereHas('subCategory', function ($query) use ($patterns) {
                                $query->where(function ($subQuery) use ($patterns) {
                                    foreach ($patterns as $index => $pattern) {
                                        $method = $index === 0 ? 'whereRaw' : 'orWhereRaw';
                                        $subQuery->{$method}('LOWER(name) LIKE ?', [$pattern]);
                                    }
                                });
                            })
                            ->when(
                                $accommodationDate,
                                fn ($query) => $query->orderByRaw('CASE WHEN id_season IS NULL THEN 0 ELSE 1 END DESC')
                            )
                            ->orderBy('price')
                            ->first();

                        if ($tariff) {
                            $tariffsByRoomType[$roomType] = $tariff;
                        }
                    }

                    if (count($tariffsByRoomType) !== $roomCounts->filter(fn ($count) => $count > 0)->count()) {
                        throw ValidationException::withMessages([
                            'room_counts' => "El hotel {$service->name_service} no tiene tarifas SPL, DBL o TPL configuradas para la distribución seleccionada.",
                        ]);
                    }

                    foreach ($accommodationGroup as $oldAccommodation) {
                        \DB::table('quote_accommodation_occupant')
                            ->where('id_quote_accommodation', $oldAccommodation->id_quote_accommodation)
                            ->delete();
                    }
                    $accommodationGroup->each->delete();

                    foreach ($roomCounts as $roomType => $roomCount) {
                        if ($roomCount < 1) {
                            continue;
                        }

                        $tariff = $tariffsByRoomType[$roomType];
                        QuoteAccommodation::create([
                            'id_quote' => $quote->id_quote,
                            'option_number' => $firstAccommodation->option_number,
                            'id_quote_day' => $firstAccommodation->id_quote_day,
                            'id_service' => $service->id_service,
                            'id_tariff' => $tariff->id_tariff,
                            'id_supplier' => $service->id_supplier,
                            'room_type' => $roomType,
                            'room_capacity' => $this->getRoomCapacity($roomType),
                            'room_count' => $roomCount,
                            'unit_price' => (float) $tariff->price,
                            'subtotal' => (float) $tariff->price * $roomCount,
                        ]);
                    }
                }
            }

            $quote->update(['passengers_count' => $passengersCount]);
            $quote->calculateTotals(1);
        });

        return redirect()->route('admin.quotes.edit', $quote)->with('success', 'Cotización calculada correctamente.');
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

        return $query->where('status', 'active')
            ->where(function ($query) use ($terms) {
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
            })
            ->where(function ($query) {
                $query->where('pricing_type', 'flat')
                    ->orWhereHas('category', fn ($categoryQuery) => $categoryQuery->where('pricing_type', 'flat'))
                    ->orWhereHas('tariffs', function ($tariffQuery) {
                        $tariffQuery->where('status', 'active')
                            ->where('pricing_type', 'flat');
                    });
            });
    }

    private function supportsFlatAccommodationPricing(Service $service): bool
    {
        if ($service->pricing_type === 'flat' || $service->category?->pricing_type === 'flat') {
            return true;
        }

        return $service->tariffs()
            ->where('status', 'active')
            ->where('pricing_type', 'flat')
            ->exists();
    }

    private function isAccommodationService(Service $service): bool
    {
        if ($service->is_accommodation) {
            return true;
        }

        $categoryIsAccommodation = (bool) ($service->category?->is_accommodation ?? false);
        if ($categoryIsAccommodation) {
            return true;
        }

        $keywords = ['hotel', 'hospedaje', 'room', 'habitacion', 'habitación', 'alojamiento', 'lodging', 'resort', 'hostel', 'suite'];
        $serviceName = strtolower((string) $service->name_service);
        $categoryName = strtolower((string) ($service->category?->name ?? ''));
        $supplierName = strtolower((string) ($service->supplier?->supplier_name ?? ''));

        foreach ($keywords as $keyword) {
            if (str_contains($serviceName, $keyword) || str_contains($categoryName, $keyword) || str_contains($supplierName, $keyword)) {
                return true;
            }
        }

        return false;
    }

    private function normalizeRoomType(?string $roomType): string
    {
        $normalized = strtolower((string) ($roomType ?? 'simple'));

        $map = [
            'single' => 'simple',
            'simple' => 'simple',
            'spl' => 'simple',
            'double' => 'doble',
            'doble' => 'doble',
            'dbl' => 'doble',
            'matrimonial' => 'doble',
            'queen' => 'doble',
            'king' => 'doble',
            'triple' => 'triple',
            'tpl' => 'triple',
        ];

        if (isset($map[$normalized])) {
            return $map[$normalized];
        }

        if (str_contains($normalized, 'tpl') || str_contains($normalized, 'triple')) {
            return 'triple';
        }

        if (str_contains($normalized, 'dbl') || str_contains($normalized, 'double') || str_contains($normalized, 'doble')) {
            return 'doble';
        }

        if (str_contains($normalized, 'spl') || str_contains($normalized, 'single') || str_contains($normalized, 'simple')) {
            return 'simple';
        }

        return 'simple';
    }

    private function getRoomCapacity(string $roomType): int
    {
        $map = [
            'simple' => 1,
            'doble' => 2,
            'triple' => 3,
        ];

        return $map[$roomType] ?? 1;
    }

    /**
     * Calcula la asignación de habitaciones (cantidad por capacidad) para un número dado de pasajeros
     * usando programación dinámica para minimizar el número de habitaciones.
     *
     * @param  array  $availableCapacities  Array de capacidades disponibles (ej. [3,2,1])
     * @return array Mapa capacity => rooms_count
     */
    private function computeRoomAllocation(int $passengers, array $availableCapacities): array
    {
        $passengers = max(1, $passengers);
        $capacities = array_values(array_unique(array_map('intval', $availableCapacities)));
        sort($capacities);

        // Programación dinámica para minimizar número de habitaciones (moneda = capacidad, minimizar monedas)
        $INF = 1_000_000;
        $dp = array_fill(0, $passengers + 1, $INF);
        $last = array_fill(0, $passengers + 1, -1);
        $dp[0] = 0;

        for ($i = 1; $i <= $passengers; $i++) {
            foreach ($capacities as $c) {
                if ($i - $c >= 0 && $dp[$i] > $dp[$i - $c] + 1) {
                    $dp[$i] = $dp[$i - $c] + 1;
                    $last[$i] = $c;
                }
            }
        }

        if ($dp[$passengers] >= $INF) {
            // Fallback: greedy highest-capacity first
            rsort($capacities);
            $remain = $passengers;
            $result = [];
            foreach ($capacities as $c) {
                $count = intdiv($remain, $c);
                if ($count > 0) {
                    $result[$c] = $count;
                }
                $remain = $remain % $c;
            }
            if ($remain > 0) {
                $smallest = min($capacities);
                $result[$smallest] = ($result[$smallest] ?? 0) + 1;
            }

            return $result;
        }

        // Reconstruir solución
        $i = $passengers;
        $counts = [];
        while ($i > 0 && $last[$i] > 0) {
            $c = $last[$i];
            $counts[$c] = ($counts[$c] ?? 0) + 1;
            $i -= $c;
        }

        return $counts;
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

            if ((int) $service->id_labels !== (int) $quote->id_labels) {
                return response()->json([
                    'success' => false,
                    'message' => 'El servicio no pertenece al mercado seleccionado para esta cotización.',
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

            $quantity = (int) ($quote->passengers_count ?: 1);
            $unitPrice = $this->resolveTariffPrice($tariff, $quantity);
            $detail = DetailQuote::firstOrCreate(
                [
                    'id_quote_day' => $day->id_quote_day,
                    'id_service' => $service->id_service,
                ],
                [
                    'id_tariff' => $tariff->id_tariff,
                    'id_supplier' => $service->id_supplier,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $unitPrice * $quantity,
                ]
            );

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
            'id_tariff' => 'nullable|exists:tariff,id_tariff',
            'unit_price' => 'nullable|numeric|min:0',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $quantity = (int) ($quote->passengers_count ?: 1);

        // If id_tariff provided, use it; otherwise try to resolve a tariff automatically
        if (! empty($data['id_tariff'])) {
            $tariff = Tariff::where('id_tariff', $data['id_tariff'])
                ->where('id_service', $detail->id_service)
                ->whereNull('id_season')
                ->where('status', 'active')
                ->firstOrFail();

            $unitPrice = $this->resolveTariffPrice($tariff, $quantity);

            $detail->update([
                'id_tariff' => $tariff->id_tariff,
                'unit_price' => $unitPrice,
                'quantity' => $quantity,
                'subtotal' => $unitPrice * $quantity,
            ]);
        } else {
            // No tariff selected: try to find an appropriate tariff for this service
            $service = Service::find($detail->id_service);
            $tariff = $this->getTariffForService($service, $quantity, $quote);

            if (! $tariff) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay una tarifa activa disponible para este servicio.',
                ], 422);
            }

            $unitPrice = $this->resolveTariffPrice($tariff, $quantity);

            $detail->update([
                'id_tariff' => $tariff->id_tariff,
                'unit_price' => $unitPrice,
                'quantity' => $quantity,
                'subtotal' => $unitPrice * $quantity,
            ]);
        }

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
            'option_number' => 'required|integer|min:1',
            'day_number' => 'nullable|integer|min:1',
            'day_start' => 'nullable|integer|min:1',
            'day_end' => 'nullable|integer|min:1|gte:day_start',
            'id_service' => 'required|exists:service,id_service',
            'id_tariff' => 'nullable|exists:tariff,id_tariff',
            'room_type' => 'nullable|string|in:simple,doble,triple,matrimonial,queen,king,single,double',
            'room_count' => 'nullable|integer|min:1',
            'room_allocations' => 'nullable|array',
            'room_allocations.*.*' => 'nullable|integer|min:0',
        ]);

        $startDay = (int) ($request->input('day_start', $request->input('day_number', 1)));
        $endDay = (int) ($request->input('day_end', $request->input('day_number', $startDay)));
        $roomType = $this->normalizeRoomType($request->input('room_type', 'simple'));
        $roomCount = max(1, (int) ($request->input('room_count', $request->input('room_quantity', 1))));
        $roomCapacity = $this->getRoomCapacity($roomType);

        if ($endDay < $startDay) {
            return response()->json([
                'success' => false,
                'message' => 'El día final no puede ser menor que el día inicial.',
            ], 422);
        }

        $service = Service::with(['category', 'tariffs'])->where('status', 'active')->find($data['id_service']);
        if (! $service || ! $this->supportsFlatAccommodationPricing($service) || ! $this->isAccommodationService($service)) {
            return response()->json([
                'success' => false,
                'message' => 'El hotel seleccionado no está disponible.',
            ], 422);
        }

        $submittedAllocations = $request->input('room_allocations', []);
        $allDayAllocations = collect($submittedAllocations['all'] ?? [])
            ->map(fn ($count) => (int) $count)
            ->filter(fn ($count) => $count > 0);
        $dayAllocations = collect($submittedAllocations)
            ->except('all')
            ->map(fn ($allocations) => collect($allocations)
                ->map(fn ($count) => (int) $count));
        $hasRoomAllocations = $allDayAllocations->isNotEmpty()
            || $dayAllocations->contains(fn ($allocations) => $allocations->contains(fn ($count) => $count > 0));

        if ($hasRoomAllocations) {
            $tariffIds = $allDayAllocations->keys()
                ->merge($dayAllocations->flatMap(fn ($allocations) => $allocations->keys()))
                ->unique()
                ->values();
            $tariffs = Tariff::with('subCategory')
                ->where('id_service', $service->id_service)
                ->where('status', 'active')
                ->whereNull('id_season')
                ->whereIn('id_tariff', $tariffIds)
                ->get()
                ->keyBy('id_tariff');

            if ($tariffs->count() !== $tariffIds->count()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Una o más tarifas no pertenecen al hotel seleccionado.',
                ], 422);
            }

            $daysAssigned = 0;
            for ($dayNumber = $startDay; $dayNumber <= $endDay; $dayNumber++) {
                $day = $quote->quoteDays()->where('day_number', $dayNumber)->first();

                if (! $day) {
                    continue;
                }

                QuoteAccommodation::where('id_quote', $quote->id_quote)
                    ->where('option_number', $data['option_number'])
                    ->where('id_quote_day', $day->id_quote_day)
                    ->where('id_service', $service->id_service)
                    ->delete();

                $roomAllocations = $allDayAllocations->toArray();
                foreach ($dayAllocations->get((string) $dayNumber, collect()) as $tariffId => $count) {
                    $roomAllocations[$tariffId] = $count;
                }
                $roomAllocations = collect($roomAllocations)->filter(fn ($count) => $count > 0);

                foreach ($roomAllocations as $tariffId => $roomCount) {
                    $selectedTariff = $tariffs->get($tariffId);
                    $roomType = $this->normalizeRoomType($selectedTariff->subCategory?->name);
                    $unitPrice = (float) $selectedTariff->price;

                    QuoteAccommodation::create([
                        'id_quote' => $quote->id_quote,
                        'option_number' => $data['option_number'],
                        'id_quote_day' => $day->id_quote_day,
                        'id_service' => $service->id_service,
                        'id_tariff' => $selectedTariff->id_tariff,
                        'id_supplier' => $service->id_supplier,
                        'room_type' => $roomType,
                        'room_capacity' => $this->getRoomCapacity($roomType),
                        'room_count' => $roomCount,
                        'unit_price' => $unitPrice,
                        'subtotal' => $unitPrice * $roomCount,
                    ]);
                }

                $daysAssigned++;
            }

            $quote->calculateTotals(1);

            return response()->json([
                'success' => true,
                'message' => 'Habitaciones guardadas para todos los días del rango.',
                'days_assigned' => $daysAssigned,
            ]);
        }

        if (! $request->filled('id_accommodation')) {
            // Register the hotel without room quantities; rooms can be defined later.
            $daysAssigned = 0;
            for ($dayNumber = $startDay; $dayNumber <= $endDay; $dayNumber++) {
                $day = $quote->quoteDays()->where('day_number', $dayNumber)->first();

                if (! $day) {
                    continue;
                }

                QuoteAccommodation::where('id_quote', $quote->id_quote)
                    ->where('option_number', $data['option_number'])
                    ->where('id_quote_day', $day->id_quote_day)
                    ->where('id_service', $service->id_service)
                    ->delete();

                QuoteAccommodation::create([
                    'id_quote' => $quote->id_quote,
                    'option_number' => $data['option_number'],
                    'id_quote_day' => $day->id_quote_day,
                    'id_service' => $service->id_service,
                    'id_tariff' => null,
                    'id_supplier' => $service->id_supplier,
                    'room_type' => null,
                    'room_capacity' => 1,
                    'room_count' => 0,
                    'unit_price' => 0,
                    'subtotal' => 0,
                ]);

                $daysAssigned++;
            }

            if ($daysAssigned === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró un día válido del itinerario para registrar el hotel.',
                ], 422);
            }

            $quote->calculateTotals(1);

            return response()->json([
                'success' => true,
                'message' => 'Hotel registrado correctamente. Las habitaciones pueden definirse después.',
                'days_assigned' => $daysAssigned,
            ]);
        }

        // If editing a single existing accommodation record, handle update directly
        if ($request->filled('id_accommodation')) {
            $accId = $request->input('id_accommodation');
            $acc = QuoteAccommodation::where('id_quote_accommodation', $accId)->where('id_quote', $quote->id_quote)->first();

            if (! $acc) {
                return response()->json(['success' => false, 'message' => 'Alojamiento no encontrado para edición.'], 404);
            }

            $roomType = $this->normalizeRoomType($request->input('room_type', $acc->room_type ?? 'simple'));
            $roomCount = max(1, (int) ($request->input('room_count', $acc->room_count ?? 1)));

            $tariff = null;
            if ($request->filled('id_tariff')) {
                $tariff = Tariff::where('id_tariff', $request->input('id_tariff'))
                    ->where('id_service', $acc->id_service)
                    ->where('status', 'active')
                    ->first();
            }

            if ($tariff) {
                $acc->id_tariff = $tariff->id_tariff;
                $acc->unit_price = (float) $tariff->price;
            }

            $acc->room_type = $roomType;
            $acc->room_capacity = $this->getRoomCapacity($roomType);
            $acc->room_count = $roomCount;
            $acc->subtotal = ($acc->unit_price ?? 0) * $roomCount;
            $acc->save();

            $quote->calculateTotals(1);

            return response()->json([
                'success' => true,
                'message' => 'Alojamiento actualizado correctamente.',
                'accommodation_id' => $acc->id_quote_accommodation,
            ]);
        }

        $tariff = ! empty($data['id_tariff'])
            ? Tariff::where('id_service', $service->id_service)
                ->where('status', 'active')
                ->find($data['id_tariff'])
            : null;

        $unitPrice = $tariff ? (float) $tariff->price : 0.0;

        $daysAssigned = [];

        // Determinar si se debe auto distribuir pasajeros en habitaciones
        $autoAllocate = false;

        if ($autoAllocate) {
            // Obtener tarifas por subcategoría para este servicio y mapear por capacidad
            $serviceTariffs = Tariff::with('subCategory')
                ->where('id_service', $service->id_service)
                ->where('status', 'active')
                ->get();

            $capacityToTariff = [];
            foreach ($serviceTariffs as $t) {
                $scLabel = $t->subCategory?->code ?? $t->subCategory?->name ?? '';
                $rt = $this->normalizeRoomType($scLabel);
                $cap = $this->getRoomCapacity($rt);

                // Guardar la tarifa más barata por capacidad disponible
                if (! isset($capacityToTariff[$cap]) || ($t->price < $capacityToTariff[$cap]->price)) {
                    $capacityToTariff[$cap] = $t;
                }
            }

            if (empty($capacityToTariff)) {
                // No hay subcategorías en las tarifas: crear la fila única como antes
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
                            'id_service' => $service->id_service,
                            'room_type' => $roomType,
                        ],
                        [
                            'id_tariff' => $tariff?->id_tariff,
                            'id_supplier' => $service->id_supplier,
                            'room_capacity' => $roomCapacity,
                            'room_count' => $roomCount,
                            'unit_price' => $unitPrice,
                            'subtotal' => $unitPrice * $roomCount,
                        ]
                    );

                    $daysAssigned[] = $accommodation->id_quote_accommodation;
                }
            } else {
                // Calcular asignación de habitaciones para el total de pasajeros
                $capacities = array_keys($capacityToTariff);
                $alloc = $this->computeRoomAllocation($passengersCount, $capacities); // mapa capacity => rooms

                for ($dayNumber = $startDay; $dayNumber <= $endDay; $dayNumber++) {
                    $day = $quote->quoteDays()->where('day_number', $dayNumber)->first();
                    if (! $day) {
                        continue;
                    }

                    // Para cada capacidad, crear/actualizar una fila de alojamiento (por tipo)
                    foreach ($alloc as $cap => $roomsCount) {
                        $roomTypeForCap = $cap === 3 ? 'triple' : ($cap === 2 ? 'doble' : 'simple');
                        $tariffForCap = $capacityToTariff[$cap];
                        $unitPriceForCap = (float) $tariffForCap->price;

                        $accommodation = QuoteAccommodation::updateOrCreate(
                            [
                                'id_quote' => $quote->id_quote,
                                'option_number' => $data['option_number'],
                                'id_quote_day' => $day->id_quote_day,
                                'id_service' => $service->id_service,
                                'room_type' => $roomTypeForCap,
                            ],
                            [
                                'id_tariff' => $tariffForCap?->id_tariff,
                                'id_supplier' => $service->id_supplier,
                                'room_capacity' => $cap,
                                'room_count' => $roomsCount,
                                'unit_price' => $unitPriceForCap,
                                'subtotal' => $unitPriceForCap * $roomsCount,
                            ]
                        );

                        $daysAssigned[] = $accommodation->id_quote_accommodation;
                    }
                }
            }
        } else {
            // Comportamiento tradicional: crear una sola fila de alojamiento según roomType/roomCount
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
                        'id_service' => $service->id_service,
                        'room_type' => $roomType,
                    ],
                    [
                        'id_tariff' => $tariff?->id_tariff,
                        'id_supplier' => $service->id_supplier,
                        'room_capacity' => $roomCapacity,
                        'room_count' => $roomCount,
                        'unit_price' => $unitPrice,
                        'subtotal' => $unitPrice * $roomCount,
                    ]
                );

                $daysAssigned[] = $accommodation->id_quote_accommodation;
            }
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
            'room_type' => $roomType,
            'room_count' => $roomCount,
            'room_capacity' => $roomCapacity,
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

    /**
     * Agregar un pasajero a la cotización
     */
    public function addPassenger(Request $request, Quote $quote)
    {
        $data = $request->validate([
            'name' => 'required|string|max:200',
            'document' => 'nullable|string|max:100',
        ]);

        $passenger = QuotePassenger::create([
            'id_quote' => $quote->id_quote,
            'name' => $data['name'],
            'document' => $data['document'] ?? null,
        ]);

        // opcional: actualizar passengers_count si está vacío
        if (! $quote->passengers_count) {
            $quote->passengers_count = $quote->passengers()->count();
            $quote->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Pasajero agregado.',
            'passenger' => $passenger,
        ]);
    }

    public function removePassenger(Quote $quote, $passengerId)
    {
        $passenger = QuotePassenger::where('id_quote_passenger', $passengerId)
            ->where('id_quote', $quote->id_quote)
            ->first();

        if (! $passenger) {
            return response()->json([
                'success' => false,
                'message' => 'Pasajero no encontrado en esta cotización.',
            ], 404);
        }

        // detach from accommodations
        $passenger->accommodations()->detach();
        $passenger->delete();

        // actualizar passengers_count
        $quote->passengers_count = $quote->passengers()->count();
        $quote->save();

        return response()->json([
            'success' => true,
            'message' => 'Pasajero eliminado.',
        ]);
    }

    /**
     * Asignar un pasajero a una fila de alojamiento (ocupante)
     */
    public function assignOccupant(Request $request, Quote $quote, QuoteAccommodation $accommodation)
    {
        $data = $request->validate([
            'id_quote_passenger' => 'required|integer',
        ]);

        if ($accommodation->id_quote !== $quote->id_quote) {
            return response()->json(['success' => false, 'message' => 'El alojamiento no pertenece a esta cotización.'], 404);
        }

        $passenger = QuotePassenger::where('id_quote_passenger', $data['id_quote_passenger'])
            ->where('id_quote', $quote->id_quote)
            ->first();

        if (! $passenger) {
            return response()->json(['success' => false, 'message' => 'Pasajero no encontrado.'], 404);
        }

        $maxOccupants = max(1, ($accommodation->room_capacity ?? 1) * ($accommodation->room_count ?? 1));
        $current = \DB::table('quote_accommodation_occupant')
            ->where('id_quote_accommodation', $accommodation->id_quote_accommodation)
            ->count();

        if ($current >= $maxOccupants) {
            return response()->json(['success' => false, 'message' => 'Capacidad máxima alcanzada para esta habitación.'], 422);
        }

        // attach (ignore if exists)
        try {
            \DB::table('quote_accommodation_occupant')->insertOrIgnore([
                'id_quote_accommodation' => $accommodation->id_quote_accommodation,
                'id_quote_passenger' => $passenger->id_quote_passenger,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json(['success' => true, 'message' => 'Pasajero asignado a la habitación.']);
        } catch (\Throwable $e) {
            \Log::error('Error al asignar ocupante: '.$e->getMessage());

            return response()->json(['success' => false, 'message' => 'No se pudo asignar el pasajero.'], 500);
        }
    }

    public function removeOccupant(Quote $quote, QuoteAccommodation $accommodation, $passengerId)
    {
        if ($accommodation->id_quote !== $quote->id_quote) {
            return response()->json(['success' => false, 'message' => 'El alojamiento no pertenece a esta cotización.'], 404);
        }

        \DB::table('quote_accommodation_occupant')
            ->where('id_quote_accommodation', $accommodation->id_quote_accommodation)
            ->where('id_quote_passenger', $passengerId)
            ->delete();

        return response()->json(['success' => true, 'message' => 'Pasajero removido de la habitación.']);
    }
}
