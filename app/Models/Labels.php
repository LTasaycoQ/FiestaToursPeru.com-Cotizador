<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Labels extends Model
{
    protected $table = 'labels';

    protected $primaryKey = 'id_labels';

    protected $fillable = [
        'name_labels',
        'status',
    ];

    public function services(): HasMany
    {
        return $this->hasMany(Service::class, 'id_labels', 'id_labels');
    }
}
