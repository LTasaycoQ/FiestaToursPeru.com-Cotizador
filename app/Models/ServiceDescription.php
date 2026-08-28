<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceDescription extends Model
{
    protected $table = 'service_descriptions';

    protected $primaryKey = 'id_service_description';

    protected $fillable = ['id_service', 'id_language', 'description'];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'id_service', 'id_service');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'id_language', 'id_language');
    }
}
