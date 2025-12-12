<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    protected $fillable = [
        'vehicle_no',
        'vehicle_type',
        'capacity',
        'driver_name',
        'driver_phone',
        'driver_license',
        'is_active',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'is_active' => 'boolean',
    ];

    public static function typeOptions(): array
    {
        return [
            'বাস' => 'বাস',
            'মাইক্রোবাস' => 'মাইক্রোবাস',
            'ভ্যান' => 'ভ্যান',
            'কার' => 'কার',
        ];
    }

    public function routes(): HasMany
    {
        return $this->hasMany(TransportRoute::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
