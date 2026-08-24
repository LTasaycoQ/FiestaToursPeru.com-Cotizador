<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table      = 'countries';
    protected $primaryKey = 'id_countries';
    protected $fillable = [
        'name', 
        'capital',
        'nationality', 
        'is_active',
    ];


    public function cities()
    {
        return $this->hasMany(City::class);
    }
}
