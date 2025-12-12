<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    protected $fillable = [
        'name',
        'grade_point',
        'min_marks',
        'max_marks',
        'remarks',
        'is_active',
    ];

    protected $casts = [
        'grade_point' => 'decimal:2',
        'min_marks' => 'decimal:2',
        'max_marks' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get grade for a given percentage
     */
    public static function getGradeForMarks(float $percentage): ?Grade
    {
        return self::where('is_active', true)
            ->where('min_marks', '<=', $percentage)
            ->where('max_marks', '>=', $percentage)
            ->first();
    }

    /**
     * Calculate GPA from array of grade points
     */
    public static function calculateGPA(array $gradePoints): float
    {
        if (empty($gradePoints))
            return 0;
        return round(array_sum($gradePoints) / count($gradePoints), 2);
    }
}
