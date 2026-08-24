<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tariff extends Model
{
    protected $table = 'tariff';

    protected $primaryKey = 'id_tariff';

    protected $fillable = [
        'id_service',
        'id_subcategories',
        'id_season',
        'pricing_type',
        'min_people_count',
        'max_people_count',
        'price',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'id_service', 'id_service');
    }

    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class, 'id_subcategories', 'id_subcategories');
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class, 'id_season', 'id_season');
    }
}
