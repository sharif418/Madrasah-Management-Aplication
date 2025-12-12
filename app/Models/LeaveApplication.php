<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveApplication extends Model
{
    protected $fillable = [
        'applicant_type',
        'applicant_id',
        'leave_type_id',
        'start_date',
        'end_date',
        'reason',
        'status',
        'approved_by',
        'approved_at',
        'rejection_reason',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    public static function statusOptions(): array
    {
        return [
            self::STATUS_PENDING => 'অপেক্ষমাণ',
            self::STATUS_APPROVED => 'অনুমোদিত',
            self::STATUS_REJECTED => 'প্রত্যাখ্যাত',
        ];
    }

    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the applicant (polymorphic-like relationship)
     */
    public function applicant()
    {
        if ($this->applicant_type === 'student') {
            return $this->belongsTo(Student::class, 'applicant_id');
        } elseif ($this->applicant_type === 'teacher') {
            return $this->belongsTo(Teacher::class, 'applicant_id');
        } elseif ($this->applicant_type === 'staff') {
            return $this->belongsTo(Staff::class, 'applicant_id');
        }
        return null;
    }

    public function getDaysCountAttribute(): int
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }
}
