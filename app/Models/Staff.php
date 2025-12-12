<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Staff extends Model
{
    use SoftDeletes;

    protected $table = 'staff';

    protected $fillable = [
        'user_id',
        'employee_id',
        'name',
        'name_en',
        'father_name',
        'date_of_birth',
        'gender',
        'marital_status',
        'religion',
        'blood_group',
        'nid',
        'phone',
        'email',
        'address',
        'designation_id',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function designation(): BelongsTo
    {
        return $this->belongsTo(Designation::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
