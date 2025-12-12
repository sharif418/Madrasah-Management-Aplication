<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HostelRoom extends Model
{
    protected $fillable = [
        'hostel_id',
        'room_no',
        'type',
        'capacity',
        'monthly_rent',
        'floor',
        'facilities',
        'status',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'monthly_rent' => 'decimal:2',
        'floor' => 'integer',
    ];

    public const TYPE_SINGLE = 'single';
    public const TYPE_DOUBLE = 'double';
    public const TYPE_TRIPLE = 'triple';
    public const TYPE_DORMITORY = 'dormitory';

    public const STATUS_AVAILABLE = 'available';
    public const STATUS_FULL = 'full';
    public const STATUS_MAINTENANCE = 'maintenance';

    public static function typeOptions(): array
    {
        return [
            self::TYPE_SINGLE => 'সিংগেল',
            self::TYPE_DOUBLE => 'ডাবল',
            self::TYPE_TRIPLE => 'ট্রিপল',
            self::TYPE_DORMITORY => 'ডরমিটরি',
        ];
    }

    public static function statusOptions(): array
    {
        return [
            self::STATUS_AVAILABLE => 'উপলব্ধ',
            self::STATUS_FULL => 'পূর্ণ',
            self::STATUS_MAINTENANCE => 'মেরামত চলছে',
        ];
    }

    public function hostel(): BelongsTo
    {
        return $this->belongsTo(Hostel::class);
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(HostelAllocation::class);
    }

    public function activeAllocations(): HasMany
    {
        return $this->hasMany(HostelAllocation::class)->where('status', 'active');
    }

    /**
     * Get occupied beds count
     */
    public function getOccupiedBedsAttribute(): int
    {
        return $this->activeAllocations()->count();
    }

    /**
     * Get available beds count
     */
    public function getAvailableBedsAttribute(): int
    {
        return max(0, $this->capacity - $this->occupied_beds);
    }

    /**
     * Check if room has space
     */
    public function hasSpace(): bool
    {
        return $this->available_beds > 0 && $this->status !== self::STATUS_MAINTENANCE;
    }

    /**
     * Update room status based on occupancy
     */
    public function updateStatus(): void
    {
        if ($this->status === self::STATUS_MAINTENANCE) {
            return;
        }

        $this->status = $this->available_beds > 0 ? self::STATUS_AVAILABLE : self::STATUS_FULL;
        $this->save();
    }
}
