<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;

    protected $table      = 'clients';
    protected $primaryKey = 'id_client';
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name_client', 'business_name', 'tax_code', 'type_client',
        'general_phone', 'general_email', 'id_cities', 'address',
    ];

    // Relación con contactos
    public function contacts()
    {
        return $this->hasMany(Contact::class, 'id_client', 'id_client');
    }

    // Relación con ciudad
    public function city()
    {
        return $this->belongsTo(City::class, 'id_cities', 'id_cities');
    }

    protected static function booted()
    {
        static::deleting(function ($client) {
            if (!$client->isForceDeleting()) {
                $client->contacts()->delete();
            }
        });

        static::restoring(function ($client) {
            $client->contacts()->withTrashed()->restore();
        });

        static::forceDeleting(function ($client) {
            $client->contacts()->forceDelete();
        });
    }

}
