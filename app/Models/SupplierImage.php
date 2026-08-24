<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierImage extends Model
{
    use SoftDeletes;

    protected $table = 'supplier_images';
    protected $primaryKey = 'id_supplier_image';

    protected $fillable = [
        'id_supplier',
        'image_path',
        'is_principal'
    ];

    protected $casts = [
        'is_principal' => 'boolean',
    ];

    // ========== RELACIONES ==========
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'id_supplier', 'id_supplier');
    }

    // ========== ACCESORES ==========
    public function getUrlAttribute()
    {
        return asset('storage/' . $this->image_path);
    }

    public function getFilenameAttribute()
    {
        return basename($this->image_path);
    }

    // ========== MUTADORES ==========
    public function setIsPrincipalAttribute($value)
    {
        // Si se marca como principal, desmarcar las demás imágenes del mismo proveedor
        if ($value && $this->id_supplier) {
            static::where('id_supplier', $this->id_supplier)
                  ->where('id_supplier_image', '!=', $this->id_supplier_image)
                  ->update(['is_principal' => false]);
        }
        $this->attributes['is_principal'] = $value;
    }

    // ========== SCOPES ==========
    public function scopePrincipal($query)
    {
        return $query->where('is_principal', true);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopeOldest($query)
    {
        return $query->orderBy('created_at', 'asc');
    }
}
