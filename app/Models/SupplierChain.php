<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierChain extends Model
{
    use SoftDeletes;

    protected $table      = 'suppliers_chains';
    protected $primaryKey = 'id_chainSupplier';
    protected $fillable = ['id_supplier', 'id_chain'];
    protected $dates = ['deleted_at'];

    public function chain()
    {
        return $this->belongsTo(Chain::class, 'id_chain', 'id_chain')->withTrashed();
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'id_supplier', 'id_supplier')->withTrashed();
    }
}
