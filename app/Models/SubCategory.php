<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubCategory extends Model
{
    protected $table = 'sub_categorie';

    protected $primaryKey = 'id_subcategories';

    protected $fillable = [
        'id_category',
        'name',
        'status',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'id_category', 'id_category');
    }

    public function serviceCategory(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'id_category', 'id_category');
    }

    public function tariffs(): HasMany
    {
        return $this->hasMany(Tariff::class, 'id_subcategories', 'id_subcategories');
    }
}
