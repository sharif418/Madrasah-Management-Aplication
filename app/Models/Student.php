<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Student extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'admission_no',
        'roll_no',
        'name',
        'name_en',
        'father_name',
        'father_phone',
        'father_occupation',
        'mother_name',
        'mother_phone',
        'date_of_birth',
        'gender',
        'religion',
        'blood_group',
        'nationality',
        'birth_certificate_no',
        'phone',
        'email',
        'present_address',
        'permanent_address',
        'guardian_id',
        'academic_year_id',
        'class_id',
        'section_id',
        'shift_id',
        'admission_date',
        'previous_school',
        'previous_class',
        'is_boarder',
        'medical_conditions',
        'status',
        'notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'admission_date' => 'date',
        'is_boarder' => 'boolean',
    ];

    /**
     * Generate unique admission number
     */
    public static function generateAdmissionNo(): string
    {
        $year = date('Y');
        $lastStudent = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $serial = $lastStudent ? ((int) substr($lastStudent->admission_no, -4)) + 1 : 1;

        return $year . str_pad($serial, 4, '0', STR_PAD_LEFT);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photo')
            ->singleFile();

        $this->addMediaCollection('documents');
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function guardian(): BelongsTo
    {
        return $this->belongsTo(Guardian::class);
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

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function marks(): HasMany
    {
        return $this->hasMany(Mark::class);
    }

    public function examResults(): HasMany
    {
        return $this->hasMany(ExamResult::class);
    }

    public function studentFees(): HasMany
    {
        return $this->hasMany(StudentFee::class);
    }

    public function feePayments(): HasMany
    {
        return $this->hasMany(FeePayment::class);
    }

    public function hifzProgress(): HasMany
    {
        return $this->hasMany(HifzProgress::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(StudentDocument::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    public function scopeInSection($query, $sectionId)
    {
        return $query->where('section_id', $sectionId);
    }

    // Accessors
    public function getAgeAttribute(): int
    {
        return $this->date_of_birth ? $this->date_of_birth->age : 0;
    }

    public function getFullInfoAttribute(): string
    {
        return $this->name . ' (' . $this->admission_no . ')';
    }

    public function getPhotoUrlAttribute(): string
    {
        $media = $this->getFirstMedia('photo');
        return $media ? $media->getUrl() : asset('images/default-student.png');
    }
}
