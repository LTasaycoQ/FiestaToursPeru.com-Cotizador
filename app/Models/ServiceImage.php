<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceImage extends Model
{

    protected $table = 'service_images';
    protected $primaryKey = 'id_service_image';

    protected $fillable = [
        'id_service',
        'image_path',
        'is_principal',
    ];

    protected $casts = [
        'is_principal' => 'boolean',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class, 'id_service', 'id_service');
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->image_path);
    }

    public function getFilenameAttribute(): string
    {
        return basename($this->image_path);
    }

    public function setIsPrincipalAttribute($value): void
    {
        if ($value && $this->id_service) {
            static::where('id_service', $this->id_service)
                ->where('id_service_image', '!=', $this->id_service_image)
                ->update(['is_principal' => false]);
        }

        $this->attributes['is_principal'] = $value;
    }
}
