<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ExamResult Model
 * 
 * পরীক্ষার সামগ্রিক ফলাফল - ছাত্রের সব বিষয়ের নম্বর একত্রিত
 * 
 * @property int $id
 * @property int $exam_id পরীক্ষা
 * @property int $student_id ছাত্র
 * @property int $class_id শ্রেণি
 * @property float $total_marks প্রাপ্ত মোট নম্বর
 * @property float $total_full_marks পূর্ণ মোট নম্বর
 * @property float|null $percentage শতাংশ
 * @property float|null $gpa জিপিএ
 * @property string|null $grade গ্রেড (A+, A, B, etc.)
 * @property int|null $position মেধা ক্রম
 * @property string|null $result_status ফলাফল স্ট্যাটাস (pass, fail, promoted, not_promoted)
 * @property string|null $remarks মন্তব্য
 */
class ExamResult extends Model
{
    protected $fillable = [
        'exam_id',
        'student_id',
        'class_id',
        'total_marks',
        'total_full_marks',
        'percentage',
        'gpa',
        'grade',
        'position',
        'result_status',
        'remarks',
    ];

    protected $casts = [
        'total_marks' => 'decimal:2',
        'total_full_marks' => 'decimal:2',
        'percentage' => 'decimal:2',
        'gpa' => 'decimal:2',
        'position' => 'integer',
    ];

    /**
     * Result Status Constants
     */
    public const STATUS_PASS = 'pass';
    public const STATUS_FAIL = 'fail';
    public const STATUS_PROMOTED = 'promoted';
    public const STATUS_NOT_PROMOTED = 'not_promoted';

    /**
     * Get status options for forms
     */
    public static function statusOptions(): array
    {
        return [
            self::STATUS_PASS => 'পাস',
            self::STATUS_FAIL => 'ফেল',
            self::STATUS_PROMOTED => 'প্রমোটেড',
            self::STATUS_NOT_PROMOTED => 'প্রমোটেড হয়নি',
        ];
    }

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
     * পাস করেছে কি না (accessor for backward compatibility)
     */
    public function getIsPassedAttribute(): bool
    {
        return $this->result_status === self::STATUS_PASS ||
            $this->result_status === self::STATUS_PROMOTED;
    }

    /**
     * Only passed results
     */
    public function scopePassed($query)
    {
        return $query->whereIn('result_status', [self::STATUS_PASS, self::STATUS_PROMOTED]);
    }

    /**
     * Only failed results
     */
    public function scopeFailed($query)
    {
        return $query->where('result_status', self::STATUS_FAIL);
    }

    /**
     * Calculate result from marks
     * 
     * @param int $examId
     * @param int $studentId
     * @return self|null
     */
    public static function calculateForStudent(int $examId, int $studentId): ?self
    {
        $marks = Mark::where('exam_id', $examId)
            ->where('student_id', $studentId)
            ->with(['subject', 'grade'])
            ->get();

        if ($marks->isEmpty()) {
            return null;
        }

        $student = Student::find($studentId);

        $totalMarks = $marks->sum('total_marks');
        $totalFullMarks = $marks->sum(fn($m) => $m->full_marks);

        $percentage = $totalFullMarks > 0
            ? round(($totalMarks / $totalFullMarks) * 100, 2)
            : 0;

        $allPassed = $marks->every(fn($m) => $m->is_passed);
        $grade = Grade::getGradeForMarks($percentage);

        // Calculate GPA (average of all subject grades)
        $gradePoints = $marks->map(function ($m) {
            return $m->grade?->grade_point ?? 0;
        })->filter()->values();

        $gpa = $gradePoints->count() > 0
            ? round($gradePoints->avg(), 2)
            : 0;

        return self::updateOrCreate(
            [
                'exam_id' => $examId,
                'student_id' => $studentId,
            ],
            [
                'class_id' => $student?->class_id,
                'total_marks' => $totalMarks,
                'total_full_marks' => $totalFullMarks,
                'percentage' => $percentage,
                'gpa' => $gpa,
                'grade' => $grade?->name,
                'result_status' => $allPassed ? self::STATUS_PASS : self::STATUS_FAIL,
            ]
        );
    }

    /**
     * Calculate positions for all students in an exam
     * 
     * @param int $examId
     * @return void
     */
    public static function calculatePositions(int $examId): void
    {
        $results = self::where('exam_id', $examId)
            ->orderBy('percentage', 'desc')
            ->get();

        $position = 1;
        $lastPercentage = null;
        $lastPosition = 1;

        foreach ($results as $result) {
            // Same percentage gets same position
            if ($lastPercentage !== null && $result->percentage == $lastPercentage) {
                $result->position = $lastPosition;
            } else {
                $result->position = $position;
                $lastPosition = $position;
            }

            $result->save();
            $lastPercentage = $result->percentage;
            $position++;
        }
    }

    /**
     * Get formatted grade with color class
     */
    public function getGradeColorClassAttribute(): string
    {
        return match ($this->grade) {
            'A+' => 'text-green-600',
            'A' => 'text-green-500',
            'A-' => 'text-blue-500',
            'B' => 'text-blue-400',
            'C' => 'text-yellow-500',
            'D' => 'text-orange-500',
            'F' => 'text-red-500',
            default => 'text-gray-500',
        };
    }

    /**
     * Get result status badge color
     */
    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->result_status) {
            self::STATUS_PASS => 'success',
            self::STATUS_FAIL => 'danger',
            self::STATUS_PROMOTED => 'info',
            self::STATUS_NOT_PROMOTED => 'warning',
            default => 'gray',
        };
    }
}
