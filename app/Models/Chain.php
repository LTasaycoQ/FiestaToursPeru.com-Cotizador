<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Chain extends Model
{
    use SoftDeletes;

    protected $table = 'chain';
    protected $primaryKey = 'id_chain';
    protected $fillable = ['name'];
    protected $dates = ['deleted_at'];

    public function suppliers(): BelongsToMany
    {
        return $this->belongsToMany(Supplier::class, 'supplier_chains', 'id_chain', 'id_supplier')
            ->withTrashed()
            ->withTimestamps();
    }
}
