<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalaryPayment extends Model
{
    protected $fillable = [
        'employee_type',
        'employee_id',
        'month',
        'year',
        'basic_salary',
        'allowances',
        'deductions',
        'bonus',
        'advance_deduction',
        'net_salary',
        'payment_date',
        'payment_method',
        'status',
        'remarks',
        'paid_by',
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'allowances' => 'decimal:2',
        'deductions' => 'decimal:2',
        'bonus' => 'decimal:2',
        'advance_deduction' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_PAID = 'paid';

    public static function statusOptions(): array
    {
        return [
            self::STATUS_PENDING => 'বকেয়া',
            self::STATUS_PAID => 'পরিশোধিত',
        ];
    }

    public static function paymentMethodOptions(): array
    {
        return [
            'cash' => 'নগদ',
            'bank' => 'ব্যাংক',
            'bkash' => 'বিকাশ',
        ];
    }

    public static function monthOptions(): array
    {
        return [
            1 => 'জানুয়ারি',
            2 => 'ফেব্রুয়ারি',
            3 => 'মার্চ',
            4 => 'এপ্রিল',
            5 => 'মে',
            6 => 'জুন',
            7 => 'জুলাই',
            8 => 'আগস্ট',
            9 => 'সেপ্টেম্বর',
            10 => 'অক্টোবর',
            11 => 'নভেম্বর',
            12 => 'ডিসেম্বর',
        ];
    }

    public function paidBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    /**
     * Get the employee (Teacher or Staff)
     */
    public function getEmployeeAttribute()
    {
        if ($this->employee_type === 'teacher') {
            return Teacher::find($this->employee_id);
        }
        return Staff::find($this->employee_id);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopePaid($query)
    {
        return $query->where('status', self::STATUS_PAID);
    }

    /**
     * Calculate net salary
     */
    public function calculateNetSalary(): float
    {
        return $this->basic_salary + $this->allowances + $this->bonus - $this->deductions - $this->advance_deduction;
    }
}
