<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalVisit extends Model
{
    protected $fillable = [
        'student_id',
        'visit_date',
        'visit_type',
        'symptoms',
        'diagnosis',
        'treatment',
        'medicines_given',
        'doctor_notes',
        'referred_to',
        'parent_informed',
        'parent_informed_date',
        'follow_up_required',
        'follow_up_date',
        'attended_by',
        'status',
    ];

    protected $casts = [
        'visit_date' => 'datetime',
        'medicines_given' => 'array',
        'parent_informed' => 'boolean',
        'parent_informed_date' => 'datetime',
        'follow_up_required' => 'boolean',
        'follow_up_date' => 'date',
    ];

    // Visit types
    public static function visitTypeOptions(): array
    {
        return [
            'regular_checkup' => 'নিয়মিত চেকআপ',
            'sick_visit' => 'অসুস্থতা',
            'injury' => 'আঘাত/দুর্ঘটনা',
            'emergency' => 'জরুরি',
            'vaccination' => 'টিকা',
            'follow_up' => 'ফলো-আপ',
            'dental' => 'দাঁত',
            'eye' => 'চোখ',
            'other' => 'অন্যান্য',
        ];
    }

    // Status options
    public static function statusOptions(): array
    {
        return [
            'treated' => 'চিকিৎসা দেওয়া হয়েছে',
            'referred' => 'রেফার করা হয়েছে',
            'sent_home' => 'বাড়ি পাঠানো হয়েছে',
            'hospitalized' => 'হাসপাতালে ভর্তি',
            'recovered' => 'সুস্থ',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function attendee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'attended_by');
    }

    // Scopes
    public function scopeToday($query)
    {
        return $query->whereDate('visit_date', today());
    }

    public function scopeEmergency($query)
    {
        return $query->where('visit_type', 'emergency');
    }

    public function scopeRequiringFollowUp($query)
    {
        return $query->where('follow_up_required', true)
            ->whereDate('follow_up_date', '<=', now());
    }
}
