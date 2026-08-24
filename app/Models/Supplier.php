<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use SoftDeletes;

    protected $table = 'suppliers';

    protected $primaryKey = 'id_supplier';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'id_categories_suppliers',
        'id_supplier_subcategory', // 👈 AGREGADO
        'supplier_name',
        'business_name',
        'tax_code',
        'general_phone',
        'general_email',
        'id_cities',
        'address',
        'description',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'id_countries', 'id_countries');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class, 'id_supplier', 'id_supplier');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(CategorySupplier::class, 'id_categories_suppliers', 'id_categories_suppliers');
    }

    /**
     * Relación con la subcategoría/clase del proveedor (Turista, Boutique, etc.)
     */
    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(SupplierSubcategory::class, 'id_supplier_subcategory', 'id_supplier_subcategory');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'id_cities', 'id_cities');
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class, 'id_supplier', 'id_supplier');
    }

    public function bankAccounts(): HasMany
    {
        return $this->hasMany(BankAccount::class, 'id_supplier', 'id_supplier');
    }

    public function chains(): BelongsToMany
    {
        return $this->belongsToMany(Chain::class, 'supplier_chains', 'id_supplier', 'id_chain')
            ->withTimestamps();
    }

    /**
     * Relación con las imágenes del proveedor
     */
    public function images(): HasMany
    {
        return $this->hasMany(SupplierImage::class, 'id_supplier', 'id_supplier')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Relación con la imagen principal del proveedor
     */
    public function principalImage(): HasOne
    {
        return $this->hasOne(SupplierImage::class, 'id_supplier', 'id_supplier')
            ->where('is_principal', true);
    }

    /**
     * Obtener la URL de la imagen principal o una imagen por defecto
     */
    public function getPrincipalImageUrlAttribute()
    {
        $image = $this->principalImage;

        return $image ? asset('storage/'.$image->image_path) : asset('images/default-supplier.png');
    }

    /**
     * Verificar si el proveedor tiene imágenes
     */
    public function hasImages(): bool
    {
        return $this->images()->count() > 0;
    }

    /**
     * Obtener las últimas N imágenes
     */
    public function getRecentImages(int $limit = 6)
    {
        return $this->images()->limit($limit)->get();
    }

    /**
     * Eventos para eliminar en cascada con soft delete
     */
    protected static function booted()
    {
        static::deleting(function ($supplier) {
            if (! $supplier->isForceDeleting()) {
                $supplier->contacts()->delete();
                $supplier->bankAccounts()->delete();
                $supplier->images()->delete();
            }
        });

        static::restoring(function ($supplier) {
            $supplier->contacts()->withTrashed()->restore();
            $supplier->bankAccounts()->withTrashed()->restore();
            $supplier->images()->withTrashed()->restore();
        });

        static::forceDeleting(function ($supplier) {
            $supplier->contacts()->forceDelete();
            $supplier->bankAccounts()->forceDelete();
            $images = $supplier->images()->withTrashed()->get();
            foreach ($images as $image) {
                if (file_exists(storage_path('app/public/'.$image->image_path))) {
                    unlink(storage_path('app/public/'.$image->image_path));
                }
                $image->forceDelete();
            }
        });
    }
}
