<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransportAllocation extends Model
{
    protected $fillable = [
        'student_id',
        'transport_route_id',
        'pickup_point',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    public static function statusOptions(): array
    {
        return [
            self::STATUS_ACTIVE => 'সক্রিয়',
            self::STATUS_INACTIVE => 'নিষ্ক্রিয়',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function transportRoute(): BelongsTo
    {
        return $this->belongsTo(TransportRoute::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Deactivate allocation
     */
    public function deactivate(): void
    {
        $this->end_date = now();
        $this->status = self::STATUS_INACTIVE;
        $this->save();
    }
}
