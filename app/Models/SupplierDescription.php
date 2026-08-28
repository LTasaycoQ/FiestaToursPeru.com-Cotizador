<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierDescription extends Model
{
    protected $table = 'supplier_descriptions';

    protected $primaryKey = 'id_supplier_description';

    protected $fillable = ['id_supplier', 'id_language', 'description'];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'id_supplier', 'id_supplier');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'id_language', 'id_language');
    }
}
