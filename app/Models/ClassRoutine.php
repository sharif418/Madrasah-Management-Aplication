<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassRoutine extends Model
{
    protected $fillable = [
        'academic_year_id',
        'class_id',
        'section_id',
        'day',
        'subject_id',
        'teacher_id',
        'start_time',
        'end_time',
        'room',
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    public static function dayOptions(): array
    {
        return [
            'saturday' => 'শনিবার',
            'sunday' => 'রবিবার',
            'monday' => 'সোমবার',
            'tuesday' => 'মঙ্গলবার',
            'wednesday' => 'বুধবার',
            'thursday' => 'বৃহস্পতিবার',
            'friday' => 'শুক্রবার',
        ];
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassName::class, 'class_id');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * Get time range
     */
    public function getTimeRangeAttribute(): string
    {
        return date('h:i A', strtotime($this->start_time)) . ' - ' . date('h:i A', strtotime($this->end_time));
    }
}
