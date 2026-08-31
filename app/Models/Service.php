<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    protected $table = 'service';

    protected $primaryKey = 'id_service';

    protected $fillable = [
        'name_service',
        'id_labels',
        'id_supplier',
        'id_category',
        'description',
        'imagen',
        'availability_days',
        'pricing_type',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relaciones
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'id_supplier', 'id_supplier');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'id_category', 'id_category');
    }

    public function labels(): BelongsTo
    {
        return $this->belongsTo(Labels::class, 'id_labels', 'id_labels');
    }

    public function tariffs(): HasMany
    {
        return $this->hasMany(Tariff::class, 'id_service', 'id_service');
    }

    public function descriptions(): HasMany
    {
        return $this->hasMany(ServiceDescription::class, 'id_service', 'id_service');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ServiceImage::class, 'id_service', 'id_service')
            ->orderBy('created_at', 'desc');
    }

    public function principalImage()
    {
        return $this->hasOne(ServiceImage::class, 'id_service', 'id_service')
            ->where('is_principal', true);
    }

    // ============================================================
    // ACCESORS
    // ============================================================

    public function getCategoryNameAttribute()
    {
        return $this->category?->name ?? 'Sin categoría';
    }

    public function getPricingTypeAttribute()
    {
        if (! is_null($this->attributes['pricing_type'] ?? null)) {
            return $this->attributes['pricing_type'];
        }

        return $this->category?->pricing_type ?? 'flat';
    }

    public function hasCustomPricingType(): bool
    {
        return ! is_null($this->attributes['pricing_type'] ?? null);
    }

    public function getCategoryPricingTypeAttribute()
    {
        return $this->category?->pricing_type ?? 'flat';
    }

    public function getIsAccommodationAttribute(): bool
    {
        return (bool) ($this->category?->is_accommodation ?? false);
    }

    public function getSupplierNameAttribute()
    {
        return $this->supplier?->supplier_name ?? 'Proveedor eliminado';
    }

    public function getPrincipalImageUrlAttribute(): string
    {
        $image = $this->principalImage()->first();

        if ($image) {
            return asset('storage/' . $image->image_path);
        }

        if ($this->imagen) {
            return asset('storage/' . $this->imagen);
        }

        return asset('images/default-supplier.png');
    }

    // ============================================================
    // SCOPES
    // ============================================================

    public function scopeAccommodations($query)
    {
        return $query->whereHas('category', function ($q) {
            $q->where('is_accommodation', true);
        });
    }

    public function scopeNonAccommodations($query)
    {
        return $query->whereHas('category', function ($q) {
            $q->where('is_accommodation', false);
        })->orWhereDoesntHave('category');
    }
}
