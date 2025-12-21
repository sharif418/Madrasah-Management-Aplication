<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Teacher extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'employee_id',
        'photo',
        'name',
        'name_en',
        'father_name',
        'mother_name',
        'date_of_birth',
        'gender',
        'marital_status',
        'religion',
        'blood_group',
        'nid',
        'phone',
        'emergency_phone',
        'email',
        'present_address',
        'permanent_address',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relation',
        'education',
        'experience',
        'specialization',
        'documents',
        'department_id',
        'designation_id',
        'joining_date',
        'basic_salary',
        'employment_type',
        'status',
        'resignation_date',
        'notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'joining_date' => 'date',
        'resignation_date' => 'date',
        'education' => 'array',
        'experience' => 'array',
        'documents' => 'array',
        'basic_salary' => 'decimal:2',
    ];

    /**
     * Generate unique employee ID
     */
    public static function generateEmployeeId(): string
    {
        $prefix = 'T';
        $year = date('y');
        $lastTeacher = self::orderBy('id', 'desc')->first();

        $serial = $lastTeacher ? ((int) substr($lastTeacher->employee_id, -4)) + 1 : 1;

        return $prefix . $year . str_pad($serial, 4, '0', STR_PAD_LEFT);
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

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function designation(): BelongsTo
    {
        return $this->belongsTo(Designation::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class, 'class_teacher_id');
    }

    public function subjectTeachers(): HasMany
    {
        return $this->hasMany(SubjectTeacher::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(StaffAttendance::class, 'attendee_id')
            ->where('attendee_type', 'teacher');
    }

    public function salaryPayments(): HasMany
    {
        return $this->hasMany(SalaryPayment::class, 'employee_id')
            ->where('employee_type', 'teacher');
    }

    public function hifzProgressRecords(): HasMany
    {
        return $this->hasMany(HifzProgress::class, 'teacher_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    // Accessors
    public function getFullNameAttribute(): string
    {
        return $this->name . ($this->designation ? ' (' . $this->designation->name . ')' : '');
    }

    public function getPhotoUrlAttribute(): string
    {
        $media = $this->getFirstMedia('photo');
        return $media ? $media->getUrl() : asset('images/default-teacher.png');
    }

    public function getServiceYearsAttribute(): int
    {
        return $this->joining_date ? $this->joining_date->diffInYears(now()) : 0;
    }
}
