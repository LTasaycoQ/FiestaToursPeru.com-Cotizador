<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceCategory extends Model
{
    protected $table = 'service_category';
    protected $primaryKey = 'id_category';

    protected $fillable = [
        'name',
        'pricing_type',
        'is_accommodation',
        'status'
    ];

    protected $casts = [
        'is_accommodation' => 'boolean',
    ];

    public function subCategories(): HasMany
    {
        return $this->hasMany(SubCategory::class, 'id_category', 'id_category');
    }
}
