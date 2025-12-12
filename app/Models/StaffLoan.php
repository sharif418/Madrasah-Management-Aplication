<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffLoan extends Model
{
    protected $fillable = [
        'loan_no',
        'staff_id',
        'loan_amount',
        'paid_amount',
        'total_installments',
        'monthly_deduction',
        'loan_date',
        'start_deduction_date',
        'status',
        'purpose',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'loan_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'monthly_deduction' => 'decimal:2',
        'loan_date' => 'date',
        'start_deduction_date' => 'date',
        'approved_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->loan_no) {
                $model->loan_no = self::generateLoanNo();
            }
            if (!$model->monthly_deduction) {
                $model->monthly_deduction = $model->loan_amount / $model->total_installments;
            }
        });
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public static function generateLoanNo(): string
    {
        $year = now()->year;
        $last = self::whereYear('created_at', $year)->orderBy('id', 'desc')->first();
        $next = $last ? (int) substr($last->loan_no, -4) + 1 : 1;
        return sprintf('LN-%d-%04d', $year, $next);
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0, $this->loan_amount - $this->paid_amount);
    }

    public function getRemainingInstallmentsAttribute(): int
    {
        if ($this->monthly_deduction <= 0)
            return 0;
        return (int) ceil($this->remaining_amount / $this->monthly_deduction);
    }

    public function approve(int $userId): void
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $userId,
            'approved_at' => now(),
        ]);
    }

    public function activate(): void
    {
        $this->update(['status' => 'active']);
    }

    public function deduct(float $amount): void
    {
        $newPaid = $this->paid_amount + $amount;
        $this->update([
            'paid_amount' => $newPaid,
            'status' => $newPaid >= $this->loan_amount ? 'completed' : 'active',
        ]);
    }

    public static function getStatusOptions(): array
    {
        return [
            'pending' => 'অপেক্ষমাণ',
            'approved' => 'অনুমোদিত',
            'active' => 'চলমান',
            'completed' => 'সম্পন্ন',
            'cancelled' => 'বাতিল',
        ];
    }
}
