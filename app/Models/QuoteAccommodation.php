<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteAccommodation extends Model
{
    protected $table = 'quote_accommodation';

    protected $primaryKey = 'id_quote_accommodation';

    protected $fillable = [
        'id_quote',
        'option_number',
        'id_quote_day',
        'id_service',
        'id_tariff',
        'id_season',
        'id_supplier',
        'room_type',
        'room_capacity',
        'room_count',
        'unit_price',
        'subtotal',
    ];

    protected $casts = [
        'option_number' => 'integer',
        'room_capacity' => 'integer',
        'room_count' => 'integer',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    // ============================================================
    // RELACIONES
    // ============================================================

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class, 'id_quote', 'id_quote');
    }

    public function quoteDay(): BelongsTo
    {
        return $this->belongsTo(QuoteDay::class, 'id_quote_day', 'id_quote_day');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'id_service', 'id_service');
    }

    public function tariff(): BelongsTo
    {
        return $this->belongsTo(Tariff::class, 'id_tariff', 'id_tariff');
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class, 'id_season', 'id_season');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'id_supplier', 'id_supplier');
    }

    /**
     * Pasajeros asignados a esta fila de alojamiento
     */
    public function occupants()
    {
        return $this->belongsToMany(
            QuotePassenger::class,
            'quote_accommodation_occupant',
            'id_quote_accommodation',
            'id_quote_passenger'
        )->withTimestamps();
    }

    // ============================================================
    // SCOPES
    // ============================================================

    public function scopeOption($query, int $optionNumber)
    {
        return $query->where('option_number', $optionNumber);
    }

    // ============================================================
    // MÉTODOS
    // ============================================================

    /**
     * Calcula el subtotal (siempre 1 noche por registro)
     */
    public function calculateSubtotal(): void
    {
        $this->subtotal = $this->unit_price * max(1, (int) ($this->room_count ?? 1));
        $this->save();
    }

    /**
     * Obtiene el número de día del itinerario
     */
    public function getDayNumberAttribute(): ?int
    {
        return $this->quoteDay?->day_number;
    }

    /**
     * Obtiene la fecha del día
     */
    public function getDateAttribute(): ?string
    {
        return $this->quoteDay?->date?->format('Y-m-d');
    }
}
