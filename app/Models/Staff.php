<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Staff extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia;

    protected $table = 'staff';

    protected $fillable = [
        'user_id',
        'employee_id',
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
        'education',
        'experience',
        'designation_id',
        'department_id',
        'joining_date',
        'basic_salary',
        'employment_type',
        'status',
        'notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'joining_date' => 'date',
        'basic_salary' => 'decimal:2',
        'education' => 'array',
        'experience' => 'array',
    ];

    /**
     * Generate unique employee ID
     */
    public static function generateEmployeeId(): string
    {
        $prefix = 'S';
        $year = date('y');
        $lastStaff = self::orderBy('id', 'desc')->first();

        $serial = $lastStaff ? ((int) substr($lastStaff->employee_id, -4)) + 1 : 1;

        return $prefix . $year . str_pad($serial, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Register media collections for photo and documents
     */
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

    public function designation(): BelongsTo
    {
        return $this->belongsTo(Designation::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(StaffAttendance::class, 'attendee_id')
            ->where('attendee_type', 'staff');
    }

    public function salaryPayments(): HasMany
    {
        return $this->hasMany(SalaryPayment::class, 'employee_id')
            ->where('employee_type', 'staff');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByDesignation($query, $designationId)
    {
        return $query->where('designation_id', $designationId);
    }

    // Accessors
    public function getFullNameAttribute(): string
    {
        return $this->name . ($this->designation ? ' (' . $this->designation->title . ')' : '');
    }

    public function getPhotoUrlAttribute(): string
    {
        $media = $this->getFirstMedia('photo');
        return $media ? $media->getUrl() : asset('images/default-staff.png');
    }

    public function getServiceYearsAttribute(): int
    {
        return $this->joining_date ? $this->joining_date->diffInYears(now()) : 0;
    }
}

