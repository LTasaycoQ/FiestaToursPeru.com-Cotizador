<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class QuotePassenger extends Model
{
    protected $table = 'quote_passengers';

    protected $primaryKey = 'id_quote_passenger';

    protected $fillable = [
        'id_quote',
        'name',
        'document',
    ];

    protected $casts = [
        // nothing special yet
    ];

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class, 'id_quote', 'id_quote');
    }

    public function accommodations(): BelongsToMany
    {
        return $this->belongsToMany(
            QuoteAccommodation::class,
            'quote_accommodation_occupant',
            'id_quote_passenger',
            'id_quote_accommodation'
        )->withTimestamps();
    }
}
