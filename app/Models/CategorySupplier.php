<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategorySupplier extends Model
{
    use SoftDeletes;

    protected $table = 'categories_suppliers';

    protected $primaryKey = 'id_categories_suppliers';

    protected $fillable = ['category_name'];

    public function suppliers()
    {
        return $this->hasMany(Supplier::class, 'id_categories_suppliers', 'id_categories_suppliers');
    }

    public function subcategories()
    {
        return $this->hasMany(SupplierSubcategory::class, 'id_categories_suppliers', 'id_categories_suppliers');
    }
}
