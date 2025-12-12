<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HostelAllocation extends Model
{
    protected $fillable = [
        'student_id',
        'hostel_id',
        'hostel_room_id',
        'bed_no',
        'allocation_date',
        'vacate_date',
        'status',
    ];

    protected $casts = [
        'allocation_date' => 'date',
        'vacate_date' => 'date',
    ];

    public const STATUS_ACTIVE = 'active';
    public const STATUS_VACATED = 'vacated';

    public static function statusOptions(): array
    {
        return [
            self::STATUS_ACTIVE => 'সক্রিয়',
            self::STATUS_VACATED => 'ছেড়ে দিয়েছে',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function hostel(): BelongsTo
    {
        return $this->belongsTo(Hostel::class);
    }

    public function hostelRoom(): BelongsTo
    {
        return $this->belongsTo(HostelRoom::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Vacate the allocation
     */
    public function vacate(): void
    {
        $this->vacate_date = now()->toDateString();
        $this->status = self::STATUS_VACATED;
        $this->save();

        // Update room status
        $this->hostelRoom?->updateStatus();
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($allocation) {
            // Update room status after allocation
            $allocation->hostelRoom?->updateStatus();
        });

        static::deleted(function ($allocation) {
            // Update room status after deletion
            $allocation->hostelRoom?->updateStatus();
        });
    }
}
