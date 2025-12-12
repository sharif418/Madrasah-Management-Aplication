<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $fillable = [
        'student_id',
        'class_id',
        'section_id',
        'academic_year_id',
        'date',
        'status',
        'in_time',      // প্রবেশের সময়
        'out_time',     // বের হওয়ার সময়
        'remarks',
        'marked_by',
    ];

    protected $casts = [
        'date' => 'date',
        'in_time' => 'datetime:H:i',
        'out_time' => 'datetime:H:i',
    ];

    // Status options
    public const STATUS_PRESENT = 'present';
    public const STATUS_ABSENT = 'absent';
    public const STATUS_LATE = 'late';
    public const STATUS_HALF_DAY = 'half_day';
    public const STATUS_LEAVE = 'leave';

    /**
     * Status options with Bengali labels
     */
    public static function statusOptions(): array
    {
        return [
            self::STATUS_PRESENT => 'উপস্থিত',
            self::STATUS_ABSENT => 'অনুপস্থিত',
            self::STATUS_LATE => 'বিলম্বে',
            self::STATUS_HALF_DAY => 'অর্ধ দিবস',
            self::STATUS_LEAVE => 'ছুটি',
        ];
    }

    /**
     * Status colors for UI
     */
    public static function statusColors(): array
    {
        return [
            self::STATUS_PRESENT => 'success',
            self::STATUS_ABSENT => 'danger',
            self::STATUS_LATE => 'warning',
            self::STATUS_HALF_DAY => 'info',
            self::STATUS_LEAVE => 'gray',
        ];
    }

    // ===== Relationships =====

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassName::class, 'class_id');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function markedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'marked_by');
    }

    // Alias for backward compatibility
    public function takenBy(): BelongsTo
    {
        return $this->markedBy();
    }

    // ===== Accessors =====

    /**
     * Get status label in Bengali
     */
    public function getStatusLabelAttribute(): string
    {
        return self::statusOptions()[$this->status] ?? $this->status;
    }

    /**
     * Get status color for badges
     */
    public function getStatusColorAttribute(): string
    {
        return self::statusColors()[$this->status] ?? 'gray';
    }

    /**
     * Check if student came late
     */
    public function getIsLateAttribute(): bool
    {
        if (!$this->in_time) {
            return $this->status === self::STATUS_LATE;
        }

        $institutionStartTime = institution_start_time() ?? '08:00';
        $startTime = \Carbon\Carbon::parse($institutionStartTime);
        $arrivalTime = \Carbon\Carbon::parse($this->in_time);

        return $arrivalTime->gt($startTime);
    }

    /**
     * Get late duration in minutes
     */
    public function getLateDurationAttribute(): ?int
    {
        if (!$this->in_time || !$this->is_late) {
            return null;
        }

        $institutionStartTime = institution_start_time() ?? '08:00';
        $startTime = \Carbon\Carbon::parse($institutionStartTime);
        $arrivalTime = \Carbon\Carbon::parse($this->in_time);

        return $arrivalTime->diffInMinutes($startTime);
    }

    /**
     * Get formatted late duration
     */
    public function getLateDurationFormattedAttribute(): ?string
    {
        $minutes = $this->late_duration;
        if (!$minutes) {
            return null;
        }

        if ($minutes < 60) {
            return "{$minutes} মিনিট দেরি";
        }

        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        return "{$hours} ঘণ্টা {$mins} মিনিট দেরি";
    }

    /**
     * Get formatted time range
     */
    public function getTimeRangeAttribute(): ?string
    {
        if (!$this->in_time && !$this->out_time) {
            return null;
        }

        $in = $this->in_time ? \Carbon\Carbon::parse($this->in_time)->format('h:i A') : '--';
        $out = $this->out_time ? \Carbon\Carbon::parse($this->out_time)->format('h:i A') : '--';

        return "{$in} - {$out}";
    }

    // ===== Scopes =====

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('date', now()->toDateString());
    }

    public function scopeForClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    public function scopeForSection($query, $sectionId)
    {
        return $query->where('section_id', $sectionId);
    }

    public function scopeForMonth($query, int $month, int $year)
    {
        return $query->whereMonth('date', $month)->whereYear('date', $year);
    }

    public function scopePresent($query)
    {
        return $query->where('status', self::STATUS_PRESENT);
    }

    public function scopeAbsent($query)
    {
        return $query->where('status', self::STATUS_ABSENT);
    }

    public function scopeLate($query)
    {
        return $query->where('status', self::STATUS_LATE);
    }

    public function scopeOnLeave($query)
    {
        return $query->where('status', self::STATUS_LEAVE);
    }

    // ===== Static Methods =====

    /**
     * Get attendance percentage for a student
     */
    public static function getStudentAttendancePercentage(int $studentId, ?int $academicYearId = null): float
    {
        $query = self::where('student_id', $studentId);

        if ($academicYearId) {
            $query->where('academic_year_id', $academicYearId);
        }

        $total = (clone $query)->count();
        if ($total === 0) {
            return 0;
        }

        $presentOrLate = (clone $query)->whereIn('status', [self::STATUS_PRESENT, self::STATUS_LATE])->count();

        return round(($presentOrLate / $total) * 100, 2);
    }

    /**
     * Get monthly summary for a student
     */
    public static function getMonthlyStudentSummary(int $studentId, int $month, int $year): array
    {
        $attendances = self::where('student_id', $studentId)
            ->forMonth($month, $year)
            ->get();

        $total = $attendances->count();
        $present = $attendances->where('status', self::STATUS_PRESENT)->count();
        $absent = $attendances->where('status', self::STATUS_ABSENT)->count();
        $late = $attendances->where('status', self::STATUS_LATE)->count();
        $leave = $attendances->where('status', self::STATUS_LEAVE)->count();
        $halfDay = $attendances->where('status', self::STATUS_HALF_DAY)->count();

        return [
            'total_days' => $total,
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'leave' => $leave,
            'half_day' => $halfDay,
            'percentage' => $total > 0 ? round((($present + $late) / $total) * 100, 1) : 0,
        ];
    }

    /**
     * Get today's attendance summary for a class
     */
    public static function getTodaySummary(?int $classId = null): array
    {
        $query = self::today();

        if ($classId) {
            $query->forClass($classId);
        }

        $total = (clone $query)->count();
        $present = (clone $query)->present()->count();
        $absent = (clone $query)->absent()->count();
        $late = (clone $query)->late()->count();
        $leave = (clone $query)->onLeave()->count();

        return [
            'total' => $total,
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'leave' => $leave,
            'percentage' => $total > 0 ? round(($present / $total) * 100, 1) : 0,
        ];
    }

    /**
     * Get classes that haven't marked attendance today
     */
    public static function getUnmarkedClasses(): array
    {
        $markedClassIds = self::today()
            ->distinct()
            ->pluck('class_id')
            ->toArray();

        return ClassName::where('is_active', true)
            ->whereNotIn('id', $markedClassIds)
            ->pluck('name', 'id')
            ->toArray();
    }
}
