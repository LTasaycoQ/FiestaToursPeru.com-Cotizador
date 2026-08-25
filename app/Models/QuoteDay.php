<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuoteDay extends Model
{
    protected $table = 'quote_day';

    protected $primaryKey = 'id_quote_day';

    protected $fillable = [
        'id_quote',
        'day_number',
        'date',
    ];

    protected $casts = [
        'date' => 'date',
        'day_number' => 'integer',
    ];

    // ============================================================
    // RELACIONES
    // ============================================================

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class, 'id_quote', 'id_quote');
    }

    public function details(): HasMany
    {
        return $this->hasMany(DetailQuote::class, 'id_quote_day', 'id_quote_day');
    }

    public function accommodations(): HasMany
    {
        return $this->hasMany(QuoteAccommodation::class, 'id_quote_day', 'id_quote_day');
    }

    public function accommodationOption1(): HasMany
    {
        return $this->accommodations()->where('option_number', 1);
    }

    public function accommodationOption2(): HasMany
    {
        return $this->accommodations()->where('option_number', 2);
    }

    // ============================================================
    // MÉTODOS
    // ============================================================

    public static function generateForQuote(Quote $quote): void
    {
        if (! $quote->start_date || ! $quote->end_date) {
            if (! $quote->days || $quote->days < 1) {
                return;
            }

            self::syncDays($quote, $quote->days, null);

            return;
        }

        $start = $quote->start_date->copy();
        $end = $quote->end_date->copy();
        $dates = [];
        while ($start->lte($end)) {
            $dates[] = $start->format('Y-m-d');
            $start->addDay();
        }

        self::syncDays($quote, count($dates), $dates);
    }

    private static function syncDays(Quote $quote, int $totalDays, ?array $dates): void
    {
        self::where('id_quote', $quote->id_quote)
            ->where('day_number', '>', $totalDays)
            ->delete();

        for ($dayNumber = 1; $dayNumber <= $totalDays; $dayNumber++) {
            self::updateOrCreate(
                [
                    'id_quote' => $quote->id_quote,
                    'day_number' => $dayNumber,
                ],
                [
                    'date' => $dates[$dayNumber - 1] ?? null,
                ]
            );
        }
    }

    public function getServicesCountAttribute(): int
    {
        return $this->details()->count();
    }

    public function canAddService(): bool
    {
        // Quitar restricción: permitir agregar cualquier cantidad de servicios por día
        return true;
    }

    /**
     * Obtiene el hotel de la opción 1 para este día
     */
    public function getHotelOption1Attribute(): ?QuoteAccommodation
    {
        return $this->accommodationOption1()->first();
    }

    /**
     * Obtiene el hotel de la opción 2 para este día
     */
    public function getHotelOption2Attribute(): ?QuoteAccommodation
    {
        return $this->accommodationOption2()->first();
    }

    /**
     * Verifica si este día tiene hotel en la opción 1
     */
    public function hasHotelOption1(): bool
    {
        return $this->accommodationOption1()->exists();
    }

    /**
     * Verifica si este día tiene hotel en la opción 2
     */
    public function hasHotelOption2(): bool
    {
        return $this->accommodationOption2()->exists();
    }
}
