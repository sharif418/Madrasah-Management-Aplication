<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KitabProgress extends Model
{
    protected $table = 'kitab_progress';

    protected $fillable = [
        'student_id',
        'kitab_id',
        'academic_year_id',
        'date',
        'chapter',
        'lesson',
        'page_from',
        'page_to',
        'teacher_notes',
        'student_notes',
        'status',
        'teacher_id',
    ];

    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_REVISION = 'revision';

    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_IN_PROGRESS => 'চলমান',
            self::STATUS_COMPLETED => 'সম্পন্ন',
            self::STATUS_REVISION => 'রিভিশন',
        ];
    }

    protected $casts = [
        'date' => 'date',
        'page_from' => 'integer',
        'page_to' => 'integer',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function kitab(): BelongsTo
    {
        return $this->belongsTo(Kitab::class, 'kitab_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * Get pages read
     */
    public function getPagesReadAttribute(): int
    {
        if ($this->page_from && $this->page_to) {
            return $this->page_to - $this->page_from + 1;
        }
        return 0;
    }
}
