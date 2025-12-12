<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalaryAdvance extends Model
{
    protected $fillable = [
        'advance_no',
        'staff_id',
        'amount',
        'advance_date',
        'reason',
        'status',
        'deducted_amount',
        'deduction_months',
        'monthly_deduction',
        'approved_by',
        'approved_at',
        'rejection_reason',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'deducted_amount' => 'decimal:2',
        'monthly_deduction' => 'decimal:2',
        'advance_date' => 'date',
        'approved_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->advance_no) {
                $model->advance_no = self::generateAdvanceNo();
            }
            if ($model->deduction_months && !$model->monthly_deduction) {
                $model->monthly_deduction = $model->amount / $model->deduction_months;
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

    public static function generateAdvanceNo(): string
    {
        $year = now()->year;
        $last = self::whereYear('created_at', $year)->orderBy('id', 'desc')->first();
        $next = $last ? (int) substr($last->advance_no, -4) + 1 : 1;
        return sprintf('ADV-%d-%04d', $year, $next);
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0, $this->amount - $this->deducted_amount);
    }

    public function approve(int $userId): void
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $userId,
            'approved_at' => now(),
        ]);
    }

    public function markAsPaid(): void
    {
        $this->update(['status' => 'paid']);
    }

    public function deduct(float $amount): void
    {
        $newDeducted = $this->deducted_amount + $amount;
        $this->update([
            'deducted_amount' => $newDeducted,
            'status' => $newDeducted >= $this->amount ? 'completed' : 'deducting',
        ]);
    }

    public static function getStatusOptions(): array
    {
        return [
            'pending' => 'অপেক্ষমাণ',
            'approved' => 'অনুমোদিত',
            'paid' => 'প্রদান করা হয়েছে',
            'deducting' => 'কর্তন চলছে',
            'completed' => 'সম্পন্ন',
            'rejected' => 'বাতিল',
        ];
    }
}
