<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = [
        'id_cities',
        'country_id',
        'name',
        'is_active'
    ];

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'id_countries');
    }


}
