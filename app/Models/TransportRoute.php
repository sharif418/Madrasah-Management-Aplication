<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransportRoute extends Model
{
    protected $fillable = [
        'name',
        'stops',
        'vehicle_id',
        'monthly_fee',
        'description',
        'is_active',
    ];

    protected $casts = [
        'stops' => 'array',
        'monthly_fee' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(TransportAllocation::class);
    }

    public function activeAllocations(): HasMany
    {
        return $this->hasMany(TransportAllocation::class)->where('status', 'active');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get student count using this route
     */
    public function getStudentCountAttribute(): int
    {
        return $this->activeAllocations()->count();
    }
}
