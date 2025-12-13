<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleMaintenance extends Model
{
    protected $fillable = [
        'vehicle_id',
        'maintenance_type',
        'description',
        'maintenance_date',
        'next_maintenance_date',
        'cost',
        'odometer_reading',
        'service_provider',
        'invoice_no',
        'notes',
        'status',
    ];

    protected $casts = [
        'maintenance_date' => 'date',
        'next_maintenance_date' => 'date',
        'cost' => 'decimal:2',
    ];

    // Maintenance type options
    public static function typeOptions(): array
    {
        return [
            'regular_service' => 'নিয়মিত সার্ভিস',
            'oil_change' => 'তেল পরিবর্তন',
            'tire_change' => 'টায়ার পরিবর্তন',
            'brake_service' => 'ব্রেক সার্ভিস',
            'engine_repair' => 'ইঞ্জিন মেরামত',
            'electrical' => 'ইলেকট্রিক্যাল',
            'ac_service' => 'এসি সার্ভিস',
            'body_work' => 'বডি ওয়ার্ক',
            'fitness_renewal' => 'ফিটনেস নবায়ন',
            'tax_renewal' => 'ট্যাক্স টোকেন নবায়ন',
            'insurance' => 'ইন্সুরেন্স',
            'other' => 'অন্যান্য',
        ];
    }

    // Status options
    public static function statusOptions(): array
    {
        return [
            'scheduled' => 'নির্ধারিত',
            'in_progress' => 'চলমান',
            'completed' => 'সম্পন্ন',
            'cancelled' => 'বাতিল',
        ];
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    // Scopes
    public function scopeUpcoming($query)
    {
        return $query->where('status', 'scheduled')
            ->whereDate('next_maintenance_date', '<=', now()->addDays(7));
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('maintenance_date', now()->month)
            ->whereYear('maintenance_date', now()->year);
    }
}
