<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailQuote extends Model
{
    protected $table = 'detail_quote';
    protected $primaryKey = 'id_detail_quote';

    protected $fillable = [
        'id_quote_day',
        'id_service',
        'id_tariff',
        'id_supplier',
        'quantity',
        'notes',
        'unit_price',
        'subtotal',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'quantity' => 'integer',
    ];

    // ============================================================
    // RELACIONES
    // ============================================================

    public function quoteDay(): BelongsTo
    {
        return $this->belongsTo(QuoteDay::class, 'id_quote_day', 'id_quote_day');
    }

    // Acceso conveniente a la cotización, saltando quote_day
    public function quote()
    {
        return $this->hasOneThrough(
            Quote::class,
            QuoteDay::class,
            'id_quote_day', // FK en quote_day
            'id_quote',     // FK en quote
            'id_quote_day', // Local key en detail_quote
            'id_quote'      // Local key en quote_day
        );
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'id_service', 'id_service');
    }

    public function tariff(): BelongsTo
    {
        return $this->belongsTo(Tariff::class, 'id_tariff', 'id_tariff');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'id_supplier', 'id_supplier');
    }

    // ============================================================
    // MÉTODOS
    // ============================================================

    public function calculateSubtotal(): void
    {
        $this->subtotal = $this->unit_price * $this->quantity;
        $this->save();
    }
}
