<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffAttendance extends Model
{
    protected $table = 'staff_attendances';

    protected $fillable = [
        'attendee_type',
        'attendee_id',
        'date',
        'status',
        'check_in',
        'check_out',
        'remarks',
        'marked_by',
    ];

    protected $casts = [
        'date' => 'date',
        'check_in' => 'datetime:H:i',
        'check_out' => 'datetime:H:i',
    ];

    public const STATUS_PRESENT = 'present';
    public const STATUS_ABSENT = 'absent';
    public const STATUS_LATE = 'late';
    public const STATUS_HALF_DAY = 'half_day';
    public const STATUS_LEAVE = 'leave';

    public static function statusOptions(): array
    {
        return [
            self::STATUS_PRESENT => 'উপস্থিত',
            self::STATUS_ABSENT => 'অনুপস্থিত',
            self::STATUS_LATE => 'দেরিতে',
            self::STATUS_HALF_DAY => 'অর্ধ দিবস',
            self::STATUS_LEAVE => 'ছুটিতে',
        ];
    }

    public static function attendeeTypeOptions(): array
    {
        return [
            'teacher' => 'শিক্ষক',
            'staff' => 'কর্মচারী',
        ];
    }

    /**
     * Get the attendee (Teacher or Staff)
     */
    public function getAttendeeAttribute()
    {
        if ($this->attendee_type === 'teacher') {
            return Teacher::find($this->attendee_id);
        }
        return Staff::find($this->attendee_id);
    }

    public function markedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'marked_by');
    }

    public function scopeTeachers($query)
    {
        return $query->where('attendee_type', 'teacher');
    }

    public function scopeStaff($query)
    {
        return $query->where('attendee_type', 'staff');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('date', today());
    }

    public function scopePresent($query)
    {
        return $query->where('status', self::STATUS_PRESENT);
    }

    public function scopeAbsent($query)
    {
        return $query->where('status', self::STATUS_ABSENT);
    }
}
