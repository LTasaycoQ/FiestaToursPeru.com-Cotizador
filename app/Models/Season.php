<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Season extends Model
{
    protected $table = 'season';

    protected $primaryKey = 'id_season';

    public $timestamps = true;

    protected $fillable = [
        'id_service',
        'name',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // Relación con Tariffs
    public function tariffs()
    {
        return $this->hasMany(Tariff::class, 'id_season', 'id_season');
    }
}
