<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hostel extends Model
{
    protected $fillable = [
        'name',
        'type',
        'address',
        'warden_name',
        'warden_phone',
        'total_rooms',
        'total_beds',
        'description',
        'is_active',
    ];

    protected $casts = [
        'total_rooms' => 'integer',
        'total_beds' => 'integer',
        'is_active' => 'boolean',
    ];

    public const TYPE_BOYS = 'boys';
    public const TYPE_GIRLS = 'girls';
    public const TYPE_MIXED = 'mixed';

    public static function typeOptions(): array
    {
        return [
            self::TYPE_BOYS => 'ছাত্র',
            self::TYPE_GIRLS => 'ছাত্রী',
            self::TYPE_MIXED => 'মিশ্র',
        ];
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(HostelRoom::class);
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(HostelAllocation::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get available beds count
     */
    public function getAvailableBedsAttribute(): int
    {
        $occupiedBeds = $this->allocations()
            ->where('status', 'active')
            ->count();
        return max(0, $this->total_beds - $occupiedBeds);
    }
}
