<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdmissionApplication extends Model
{
    protected $fillable = [
        'application_no',
        'academic_year_id',
        'class_id',
        'student_name',
        'student_name_en',
        'date_of_birth',
        'gender',
        'blood_group',
        'birth_certificate_no',
        'father_name',
        'father_phone',
        'father_occupation',
        'mother_name',
        'mother_phone',
        'present_address',
        'permanent_address',
        'previous_school',
        'previous_class',
        'photo',
        'birth_certificate',
        'previous_certificate',
        'status',
        'remarks',
        'processed_by',
        'processed_at',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'processed_at' => 'datetime',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_ADMITTED = 'admitted';

    public static function statusOptions(): array
    {
        return [
            self::STATUS_PENDING => 'অপেক্ষমাণ',
            self::STATUS_APPROVED => 'অনুমোদিত',
            self::STATUS_REJECTED => 'প্রত্যাখ্যাত',
            self::STATUS_ADMITTED => 'ভর্তি সম্পন্ন',
        ];
    }

    public static function genderOptions(): array
    {
        return [
            'male' => 'ছেলে',
            'female' => 'মেয়ে',
        ];
    }

    /**
     * Generate application number
     */
    public static function generateApplicationNo(): string
    {
        $year = date('Y');
        $last = self::whereYear('created_at', $year)->orderBy('id', 'desc')->first();
        $serial = $last ? ((int) substr($last->application_no, -5)) + 1 : 1;
        return 'ADM' . $year . str_pad($serial, 5, '0', STR_PAD_LEFT);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassName::class, 'class_id');
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Approve application
     */
    public function approve(int $userId): void
    {
        $this->status = self::STATUS_APPROVED;
        $this->processed_by = $userId;
        $this->processed_at = now();
        $this->save();
    }

    /**
     * Reject application
     */
    public function reject(int $userId, string $remarks = null): void
    {
        $this->status = self::STATUS_REJECTED;
        $this->processed_by = $userId;
        $this->processed_at = now();
        $this->remarks = $remarks;
        $this->save();
    }
}
