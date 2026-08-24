<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use SoftDeletes;

    protected $table      = 'contacts';
    protected $primaryKey = 'id_contacts';
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'id_client', 'id_supplier', 'name', 'last_names',
        'qualification', 'email',
        'first_phone', 'second_phone', 'es_principal',
    ];

    protected $casts = ['es_principal' => 'boolean'];

    public function client()
    {
        return $this->belongsTo(Client::class, 'id_client', 'id_client')->withTrashed();
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'id_supplier', 'id_supplier')->withTrashed();
    }
}
