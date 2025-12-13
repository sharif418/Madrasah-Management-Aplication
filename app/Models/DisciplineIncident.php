<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DisciplineIncident extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'student_id',
        'academic_year_id',
        'incident_date',
        'incident_type',
        'severity',
        'description',
        'location',
        'witnesses',
        'reported_by',
        'action_taken',
        'action_date',
        'parent_notified',
        'parent_notified_date',
        'parent_meeting_date',
        'parent_meeting_notes',
        'follow_up_required',
        'follow_up_date',
        'follow_up_notes',
        'status',
        'merit_points',
        'notes',
    ];

    protected $casts = [
        'incident_date' => 'date',
        'action_date' => 'date',
        'parent_notified' => 'boolean',
        'parent_notified_date' => 'date',
        'parent_meeting_date' => 'date',
        'follow_up_required' => 'boolean',
        'follow_up_date' => 'date',
        'merit_points' => 'integer',
    ];

    // Incident Types
    public static function incidentTypeOptions(): array
    {
        return [
            'late_arrival' => 'দেরিতে আসা',
            'absent_without_leave' => 'বিনা অনুমতিতে অনুপস্থিতি',
            'dress_code' => 'পোশাক বিধি লঙ্ঘন',
            'disrespect' => 'অসম্মান/অভদ্রতা',
            'fighting' => 'মারামারি',
            'bullying' => 'বুলিং',
            'cheating' => 'পরীক্ষায় নকল',
            'property_damage' => 'সম্পত্তি ক্ষতি',
            'mobile_use' => 'মোবাইল ব্যবহার',
            'leaving_early' => 'সময়ের আগে চলে যাওয়া',
            'disobedience' => 'অবাধ্যতা',
            'other' => 'অন্যান্য',
        ];
    }

    // Severity Levels
    public static function severityOptions(): array
    {
        return [
            'minor' => 'হালকা',
            'moderate' => 'মাঝারি',
            'serious' => 'গুরুতর',
            'severe' => 'অত্যন্ত গুরুতর',
        ];
    }

    // Actions
    public static function actionOptions(): array
    {
        return [
            'verbal_warning' => 'মৌখিক সতর্কতা',
            'written_warning' => 'লিখিত সতর্কতা',
            'parent_meeting' => 'অভিভাবক সাক্ষাৎ',
            'detention' => 'ডিটেনশন',
            'community_service' => 'সেবামূলক কাজ',
            'suspension' => 'সাময়িক বহিষ্কার',
            'expulsion' => 'স্থায়ী বহিষ্কার',
            'counseling' => 'কাউন্সেলিং',
            'fine' => 'জরিমানা',
            'none' => 'কোন ব্যবস্থা নেই',
        ];
    }

    // Status Options
    public static function statusOptions(): array
    {
        return [
            'reported' => 'রিপোর্টেড',
            'investigating' => 'তদন্ত চলছে',
            'action_taken' => 'ব্যবস্থা নেওয়া হয়েছে',
            'resolved' => 'সমাধান হয়েছে',
            'closed' => 'বন্ধ',
        ];
    }

    // Relationships
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    // Scopes
    public function scopeThisYear($query)
    {
        $currentYear = AcademicYear::where('is_current', true)->first();
        return $query->where('academic_year_id', $currentYear?->id);
    }

    public function scopeOpen($query)
    {
        return $query->whereIn('status', ['reported', 'investigating']);
    }

    public function scopeSerious($query)
    {
        return $query->whereIn('severity', ['serious', 'severe']);
    }

    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    // Accessors
    public function getSeverityColorAttribute(): string
    {
        return match ($this->severity) {
            'minor' => 'info',
            'moderate' => 'warning',
            'serious' => 'danger',
            'severe' => 'danger',
            default => 'gray',
        };
    }
}
