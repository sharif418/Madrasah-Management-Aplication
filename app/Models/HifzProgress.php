<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HifzProgress extends Model
{
    protected $table = 'hifz_progress';

    protected $fillable = [
        'student_id',
        'academic_year_id',
        'date',
        // Sabaq (নতুন পড়া)
        'sabaq_para',
        'sabaq_surah',
        'sabaq_ayat_from',
        'sabaq_ayat_to',
        'sabaq_lines',
        'sabaq_quality',
        // Sabqi (গত দিনের পড়া)
        'sabqi_para',
        'sabqi_surah',
        'sabqi_quality',
        // Manzil (দোহার)
        'manzil_para_from',
        'manzil_para_to',
        'manzil_quality',
        // Tajweed & Qirat
        'tajweed_lesson',
        'tajweed_quality',
        'qirat_surah',
        'qirat_quality',
        'teacher_remarks',
        'teacher_id',
    ];

    protected $casts = [
        'date' => 'date',
        'sabaq_para' => 'integer',
        'sabaq_ayat_from' => 'integer',
        'sabaq_ayat_to' => 'integer',
        'sabaq_lines' => 'integer',
        'sabqi_para' => 'integer',
        'manzil_para_from' => 'integer',
        'manzil_para_to' => 'integer',
    ];

    public static function qualityOptions(): array
    {
        return [
            'excellent' => 'অতি উত্তম',
            'good' => 'উত্তম',
            'average' => 'মধ্যম',
            'poor' => 'দুর্বল',
        ];
    }

    public static function paraOptions(): array
    {
        $paras = [];
        for ($i = 1; $i <= 30; $i++) {
            $paras[$i] = "পারা {$i}";
        }
        return $paras;
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
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
     * Get quality color for display
     */
    public static function getQualityColor(string $quality): string
    {
        return match ($quality) {
            'excellent' => 'success',
            'good' => 'info',
            'average' => 'warning',
            'poor' => 'danger',
            default => 'gray',
        };
    }
}
