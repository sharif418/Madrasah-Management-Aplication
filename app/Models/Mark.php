<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Mark Model
 * 
 * পরীক্ষার নম্বর ব্যবস্থাপনা - বিষয়ভিত্তিক নম্বর সংরক্ষণ
 * 
 * @property int $id
 * @property int $exam_id পরীক্ষা
 * @property int $student_id ছাত্র
 * @property int $class_id শ্রেণি
 * @property int $subject_id বিষয়
 * @property float|null $written_marks লিখিত নম্বর
 * @property float|null $mcq_marks বহুনির্বাচনী নম্বর
 * @property float|null $practical_marks ব্যবহারিক নম্বর
 * @property float|null $viva_marks মৌখিক নম্বর
 * @property float|null $total_marks মোট নম্বর
 * @property int|null $grade_id গ্রেড
 * @property bool $is_absent অনুপস্থিত ছিল কি না
 * @property string|null $remarks মন্তব্য
 * @property int|null $entered_by নম্বর এন্ট্রি কে করেছে
 */
class Mark extends Model
{
    protected $fillable = [
        'exam_id',
        'student_id',
        'class_id',
        'subject_id',
        'written_marks',
        'mcq_marks',
        'practical_marks',
        'viva_marks',
        'total_marks',
        'grade_id',
        'is_absent',
        'remarks',
        'entered_by',
    ];

    protected $casts = [
        'written_marks' => 'decimal:2',
        'mcq_marks' => 'decimal:2',
        'practical_marks' => 'decimal:2',
        'viva_marks' => 'decimal:2',
        'total_marks' => 'decimal:2',
        'is_absent' => 'boolean',
    ];

    /**
     * পরীক্ষা
     */
    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * ছাত্র
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * শ্রেণি
     */
    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassName::class, 'class_id');
    }

    /**
     * বিষয়
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * গ্রেড
     */
    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }

    /**
     * নম্বর এন্ট্রি কে করেছে
     */
    public function enteredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'entered_by');
    }

    /**
     * মোট নম্বর গণনা করা (auto calculate)
     */
    public function calculateTotalMarks(): float
    {
        return ($this->written_marks ?? 0) +
            ($this->mcq_marks ?? 0) +
            ($this->practical_marks ?? 0) +
            ($this->viva_marks ?? 0);
    }

    /**
     * পরীক্ষায় পাস করেছে কি না
     * ExamSchedule থেকে pass_marks নিয়ে compare করে
     */
    public function getIsPassedAttribute(): bool
    {
        if ($this->is_absent) {
            return false;
        }

        // Get pass marks from exam schedule
        $schedule = ExamSchedule::where('exam_id', $this->exam_id)
            ->where('subject_id', $this->subject_id)
            ->first();

        $passMarks = $schedule?->pass_marks ?? 33; // Default 33%
        $fullMarks = $schedule?->full_marks ?? 100;

        // Calculate pass marks in absolute value
        $requiredMarks = ($fullMarks * $passMarks) / 100;

        return $this->total_marks >= $requiredMarks;
    }

    /**
     * শতাংশ নম্বর
     */
    public function getPercentageAttribute(): float
    {
        $schedule = ExamSchedule::where('exam_id', $this->exam_id)
            ->where('subject_id', $this->subject_id)
            ->first();

        $fullMarks = $schedule?->full_marks ?? 100;

        if ($fullMarks == 0) {
            return 0;
        }

        return round(($this->total_marks / $fullMarks) * 100, 2);
    }

    /**
     * Full marks accessor (from ExamSchedule)
     */
    public function getFullMarksAttribute(): float
    {
        $schedule = ExamSchedule::where('exam_id', $this->exam_id)
            ->where('subject_id', $this->subject_id)
            ->first();

        return $schedule?->full_marks ?? 100;
    }

    /**
     * Pass marks accessor (from ExamSchedule)
     */
    public function getPassMarksAttribute(): float
    {
        $schedule = ExamSchedule::where('exam_id', $this->exam_id)
            ->where('subject_id', $this->subject_id)
            ->first();

        return $schedule?->pass_marks ?? 33;
    }

    /**
     * Alias for backwards compatibility
     * @deprecated Use total_marks instead
     */
    public function getMarksObtainedAttribute(): float
    {
        return $this->total_marks ?? 0;
    }

    /**
     * Saving এর সময় total marks auto calculate করা
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($mark) {
            // Auto calculate total marks
            $mark->total_marks = $mark->calculateTotalMarks();

            // If absent, set all marks to 0
            if ($mark->is_absent) {
                $mark->written_marks = 0;
                $mark->mcq_marks = 0;
                $mark->practical_marks = 0;
                $mark->viva_marks = 0;
                $mark->total_marks = 0;
            }

            // Auto assign grade
            if ($mark->total_marks !== null && !$mark->is_absent) {
                $percentage = $mark->percentage;
                $grade = Grade::getGradeForMarks($percentage);
                $mark->grade_id = $grade?->id;
            }
        });
    }
}
