<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierSubcategory extends Model
{
    protected $table = 'supplier_subcategories';

    protected $primaryKey = 'id_supplier_subcategory';

    protected $fillable = ['id_categories_suppliers', 'name'];

    public function categorySupplier()
    {
        return $this->belongsTo(CategorySupplier::class, 'id_categories_suppliers', 'id_categories_suppliers');
    }

    public function suppliers()
    {
        return $this->hasMany(Supplier::class, 'id_supplier_subcategory', 'id_supplier_subcategory');
    }
}
