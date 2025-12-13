<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FuelLog extends Model
{
    protected $fillable = [
        'vehicle_id',
        'driver_id',
        'date',
        'fuel_type',
        'quantity',
        'rate',
        'total_cost',
        'odometer_reading',
        'fuel_station',
        'receipt_no',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'quantity' => 'decimal:2',
        'rate' => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];

    // Fuel type options
    public static function fuelTypeOptions(): array
    {
        return [
            'diesel' => 'ডিজেল',
            'petrol' => 'পেট্রোল',
            'octane' => 'অকটেন',
            'cng' => 'সিএনজি',
            'lpg' => 'এলপিজি',
        ];
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'driver_id');
    }

    // Scopes
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('date', now()->month)
            ->whereYear('date', now()->year);
    }

    public function scopeForVehicle($query, $vehicleId)
    {
        return $query->where('vehicle_id', $vehicleId);
    }

    // Calculate mileage
    public function getMileageAttribute()
    {
        $previousLog = FuelLog::where('vehicle_id', $this->vehicle_id)
            ->where('id', '<', $this->id)
            ->orderBy('id', 'desc')
            ->first();

        if (!$previousLog || !$this->odometer_reading || !$previousLog->odometer_reading) {
            return null;
        }

        $distance = $this->odometer_reading - $previousLog->odometer_reading;
        return $distance > 0 && $this->quantity > 0
            ? round($distance / $this->quantity, 2)
            : null;
    }
}
