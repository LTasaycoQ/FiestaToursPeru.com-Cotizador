<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class Quote extends Model
{
    use SoftDeletes;

    protected $table = 'quote';

    protected $primaryKey = 'id_quote';

    protected $fillable = [
        'id_client',
        'id_users',
        'id_contacts',
        'id_labels',
        'id_language',
        'name',
        'quote_number',
        'correlative',
        'correlative_assigned_at',
        'status',
        'days',
        'start_date',
        'end_date',
        'expiration_date',
        'passengers_count',
        'subtotal',
        'total',
        'currency',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'expiration_date' => 'date',
        'subtotal' => 'decimal:2',
        'total' => 'decimal:2',
        'days' => 'integer',
        'passengers_count' => 'integer',
        'correlative_assigned_at' => 'datetime',
    ];

    // ============================================================
    // RELACIONES
    // ============================================================

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'id_client', 'id_client');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_users', 'id');
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'id_contacts', 'id_contacts');
    }

    public function market(): BelongsTo
    {
        return $this->belongsTo(Labels::class, 'id_labels', 'id_labels');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'id_language', 'id_language');
    }

    public function quoteDays(): HasMany
    {
        return $this->hasMany(QuoteDay::class, 'id_quote', 'id_quote')->orderBy('day_number');
    }

    public function details(): HasManyThrough
    {
        return $this->hasManyThrough(
            DetailQuote::class,
            QuoteDay::class,
            'id_quote',
            'id_quote_day',
            'id_quote',
            'id_quote_day'
        );
    }

    public function accommodations(): HasMany
    {
        return $this->hasMany(QuoteAccommodation::class, 'id_quote', 'id_quote');
    }

    public function accommodationOption1(): HasMany
    {
        return $this->accommodations()->where('option_number', 1);
    }

    public function accommodationOption2(): HasMany
    {
        return $this->accommodations()->where('option_number', 2);
    }

    /**
     * Pasajeros (viajeros) asignados a esta cotización
     */
    public function passengers(): HasMany
    {
        return $this->hasMany(QuotePassenger::class, 'id_quote', 'id_quote');
    }

    // ============================================================
    // MÉTODOS DE ITINERARIO
    // ============================================================

    public function generateItineraryDays(): void
    {
        QuoteDay::generateForQuote($this);

        if ($this->start_date && $this->end_date) {
            $this->days = $this->start_date->diffInDays($this->end_date) + 1;
            $this->save();
        }
    }

    // ============================================================
    // MÉTODOS DE HOTELES
    // ============================================================

    /**
     * Obtiene las opciones de hotel agrupadas por día
     */
    public function getAccommodationByDay(int $optionNumber = 1): array
    {
        return $this->accommodations()
            ->where('option_number', $optionNumber)
            ->with('quoteDay')
            ->get()
            ->groupBy('id_quote_day')
            ->map(function ($items) {
                return $items->first();
            })
            ->toArray();
    }

    /**
     * Obtiene la cobertura de hoteles por día para una opción
     */
    public function getAccommodationCoverage(int $optionNumber = 1): array
    {
        $days = $this->quoteDays()->pluck('id_quote_day')->toArray();
        $accommodatedDays = $this->accommodations()
            ->where('option_number', $optionNumber)
            ->pluck('id_quote_day')
            ->toArray();

        return [
            'total_days' => count($days),
            'covered_days' => count(array_intersect($days, $accommodatedDays)),
            'missing_days' => array_diff($days, $accommodatedDays),
        ];
    }

    /**
     * Verifica si todos los días tienen hotel para una opción
     */
    public function hasCompleteAccommodation(int $optionNumber = 1): bool
    {
        $coverage = $this->getAccommodationCoverage($optionNumber);

        return $coverage['covered_days'] === $coverage['total_days'];
    }

    /**
     * Agrega un hotel para un día específico
     */
    public function addAccommodationForDay(
        int $optionNumber,
        int $quoteDayId,
        int $serviceId,
        int $tariffId,
        int $supplierId,
        float $unitPrice
    ): QuoteAccommodation {
        return $this->accommodations()->create([
            'option_number' => $optionNumber,
            'id_quote_day' => $quoteDayId,
            'id_service' => $serviceId,
            'id_tariff' => $tariffId,
            'id_supplier' => $supplierId,
            'unit_price' => $unitPrice,
            'subtotal' => $unitPrice,
        ]);
    }

    /**
     * Agrega el mismo hotel para múltiples días
     */
    public function addAccommodationForDays(
        int $optionNumber,
        array $quoteDayIds,
        int $serviceId,
        int $tariffId,
        int $supplierId,
        float $unitPrice
    ): array {
        $records = [];
        foreach ($quoteDayIds as $dayId) {
            $records[] = $this->addAccommodationForDay(
                $optionNumber,
                $dayId,
                $serviceId,
                $tariffId,
                $supplierId,
                $unitPrice
            );
        }

        return $records;
    }

    /**
     * Clona los hoteles de la opción 1 a la opción 2
     */
    public function cloneAccommodationOption1ToOption2(): void
    {
        $option1Hotels = $this->accommodationOption1()->get();

        // Eliminar opción 2 existente
        $this->accommodationOption2()->delete();

        foreach ($option1Hotels as $hotel) {
            $this->accommodations()->create([
                'option_number' => 2,
                'id_quote_day' => $hotel->id_quote_day,
                'id_service' => $hotel->id_service,
                'id_tariff' => $hotel->id_tariff,
                'id_supplier' => $hotel->id_supplier,
                'unit_price' => $hotel->unit_price,
                'subtotal' => $hotel->unit_price,
            ]);
        }
    }

    // ============================================================
    // MÉTODOS DE CÁLCULO
    // ============================================================

    public function calculateTotals(int $accommodationOption = 1): void
    {
        $itineraryTotal = $this->details()->sum('subtotal');
        $checkoutDay = $this->quoteDays()->max('day_number');
        $accommodationTotal = $this->accommodations()
            ->where('option_number', $accommodationOption)
            ->when($checkoutDay, fn ($query) => $query->whereHas('quoteDay', fn ($dayQuery) => $dayQuery->where('day_number', '<', $checkoutDay)))
            ->sum('subtotal');

        $this->subtotal = $itineraryTotal + $accommodationTotal;
        $this->total = $this->subtotal;
        $this->save();
    }

    public function getTotalsByOption(): array
    {
        $itineraryTotal = $this->details()->sum('subtotal');
        $checkoutDay = $this->quoteDays()->max('day_number');

        return [
            1 => $itineraryTotal + $this->accommodations()->where('option_number', 1)->when($checkoutDay, fn ($query) => $query->whereHas('quoteDay', fn ($dayQuery) => $dayQuery->where('day_number', '<', $checkoutDay)))->sum('subtotal'),
            2 => $itineraryTotal + $this->accommodations()->where('option_number', 2)->when($checkoutDay, fn ($query) => $query->whereHas('quoteDay', fn ($dayQuery) => $dayQuery->where('day_number', '<', $checkoutDay)))->sum('subtotal'),
        ];
    }

    public function getTariffForService(Service $service, int $passengersCount): ?Tariff
    {
        $mode = $service->pricing_type;

        if ($mode === 'flat') {
            return Tariff::where('id_service', $service->id_service)
                ->whereNull('id_season')
                ->where('status', 'active')
                ->first();
        }

        return Tariff::where('id_service', $service->id_service)
            ->whereNull('id_season')
            ->where('status', 'active')
            ->where('min_people_count', '<=', $passengersCount)
            ->where(function ($query) use ($passengersCount) {
                $query->where('max_people_count', '>=', $passengersCount)
                    ->orWhereNull('max_people_count');
            })
            ->orderBy('min_people_count')
            ->first();
    }

    // ============================================================
    // MÉTODOS PARA EL CORRELATIVO
    // ============================================================

    public static function generateCorrelative(string $startDate): string
    {
        $date = new \DateTimeImmutable($startDate);
        $month = (int) $date->format('m');
        $year = $date->format('y');
        $yearFull = $date->format('Y');

        $lastNumber = 100;
        $existingCorrelatives = self::query()
            ->whereNotNull('correlative')
            ->whereMonth('start_date', $month)
            ->whereYear('start_date', $yearFull)
            ->pluck('correlative')
            ->filter();

        foreach ($existingCorrelatives as $correlative) {
            $parts = preg_split('/-/', trim((string) $correlative));
            if (count($parts) !== 3) {
                continue;
            }

            $number = (int) ($parts[1] ?? 0);
            if ($number > $lastNumber) {
                $lastNumber = $number;
            }
        }

        $nextNumber = $lastNumber >= 101 ? $lastNumber + 1 : 101;

        if ($nextNumber > 999) {
            Log::warning('Se alcanzó el límite superior de correlativos para el mes', [
                'month' => $month,
                'year' => $yearFull,
                'last_number' => $lastNumber,
            ]);
            $nextNumber = 101;
        }

        return sprintf('%02d-%03d-%02d', $month, $nextNumber, $year);
    }

    public function assignCorrelative(): bool
    {
        if ($this->status !== 'Confirmado' || $this->correlative) {
            return false;
        }

        if (! $this->start_date) {
            Log::warning('Intento de asignar correlativo sin start_date', [
                'quote_id' => $this->id_quote,
                'quote_number' => $this->quote_number,
            ]);

            return false;
        }

        $this->correlative = self::generateCorrelative($this->start_date->format('Y-m-d'));
        $this->correlative_assigned_at = now();
        $this->save();

        return true;
    }

    public function hasCorrelative(): bool
    {
        return ! empty($this->correlative);
    }

    public function getFormattedCorrelativeAttribute(): string
    {
        return $this->correlative ?? 'Sin asignar';
    }

    public function isValidCorrelative(): bool
    {
        if (! $this->correlative) {
            return false;
        }

        $pattern = '/^\d{2}-\d{3}-\d{2}$/';
        if (! preg_match($pattern, $this->correlative)) {
            return false;
        }

        $parts = explode('-', $this->correlative);
        $number = intval($parts[1]);

        return $number >= 100;
    }

    // ============================================================
    // ACCESSORS
    // ============================================================

    public function getStatusLabelAttribute(): string
    {
        return [
            'Recibido' => 'Recibido',
            'Enviado' => 'Enviado',
            'Confirmado' => 'Confirmado',
            'Reconfirmado' => 'Reconfirmado',
            'Cancelado' => 'Cancelado',
            'Borrador' => 'Borrador',
        ][$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return [
            'Recibido' => 'blue',
            'Enviado' => 'orange',
            'Confirmado' => 'green',
            'Reconfirmado' => 'purple',
            'Cancelado' => 'red',
            'Borrador' => 'gray',
        ][$this->status] ?? 'gray';
    }

    public function getCurrencySymbolAttribute(): string
    {
        return [
            'PEN' => 'S/',
            'USD' => '$',
            'EUR' => '€',
        ][$this->currency] ?? 'S/';
    }
}
