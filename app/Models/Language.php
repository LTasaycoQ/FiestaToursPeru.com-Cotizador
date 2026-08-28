<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Language extends Model
{
    protected $table = 'languages';

    protected $primaryKey = 'id_language';

    protected $fillable = ['name', 'code', 'status'];

    public function serviceDescriptions(): HasMany
    {
        return $this->hasMany(ServiceDescription::class, 'id_language', 'id_language');
    }

    public function supplierDescriptions(): HasMany
    {
        return $this->hasMany(SupplierDescription::class, 'id_language', 'id_language');
    }
}
