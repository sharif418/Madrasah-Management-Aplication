<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentHealth extends Model
{
    protected $fillable = [
        'student_id',
        'height',
        'weight',
        'blood_group',
        'vision_left',
        'vision_right',
        'hearing_status',
        'allergies',
        'chronic_conditions',
        'disabilities',
        'current_medications',
        'past_surgeries',
        'family_medical_history',
        'immunization_records',
        'last_physical_exam',
        'doctor_name',
        'doctor_phone',
        'emergency_hospital',
        'insurance_info',
        'special_dietary_needs',
        'notes',
    ];

    protected $casts = [
        'allergies' => 'array',
        'chronic_conditions' => 'array',
        'current_medications' => 'array',
        'past_surgeries' => 'array',
        'immunization_records' => 'array',
        'last_physical_exam' => 'date',
        'height' => 'decimal:2',
        'weight' => 'decimal:2',
    ];

    // Blood group options
    public static function bloodGroupOptions(): array
    {
        return [
            'A+' => 'A+',
            'A-' => 'A-',
            'B+' => 'B+',
            'B-' => 'B-',
            'AB+' => 'AB+',
            'AB-' => 'AB-',
            'O+' => 'O+',
            'O-' => 'O-',
        ];
    }

    // Common allergies
    public static function commonAllergies(): array
    {
        return [
            'ধুলা/ধোঁয়া',
            'ঠান্ডা',
            'চিংড়ি/সামুদ্রিক মাছ',
            'বাদাম',
            'দুধ',
            'ডিম',
            'গম/গ্লুটেন',
            'পেনিসিলিন',
            'সালফা',
            'মৌমাছির হুল',
            'অন্যান্য'
        ];
    }

    // Hearing status
    public static function hearingOptions(): array
    {
        return [
            'normal' => 'স্বাভাবিক',
            'mild_loss' => 'হালকা সমস্যা',
            'moderate_loss' => 'মাঝারি সমস্যা',
            'severe_loss' => 'গুরুতর সমস্যা',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    // Calculate BMI
    public function getBmiAttribute(): ?float
    {
        if (!$this->height || !$this->weight || $this->height == 0) {
            return null;
        }
        $heightInMeters = $this->height / 100;
        return round($this->weight / ($heightInMeters * $heightInMeters), 1);
    }

    public function getBmiCategoryAttribute(): string
    {
        $bmi = $this->bmi;
        if (!$bmi)
            return 'অজানা';

        if ($bmi < 18.5)
            return 'আন্ডারওয়েট';
        if ($bmi < 25)
            return 'স্বাভাবিক';
        if ($bmi < 30)
            return 'ওভারওয়েট';
        return 'স্থূলতা';
    }
}
