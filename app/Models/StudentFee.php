<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentFee extends Model
{
    protected $fillable = [
        'student_id',
        'fee_structure_id',
        'fee_discount_id',
        'month',
        'year',
        'original_amount',
        'discount_amount',
        'final_amount',
        'paid_amount',
        'due_amount',
        'due_date',
        'status',
        'is_installment',
        'total_installments',
    ];

    protected $casts = [
        'original_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
        'due_date' => 'date',
        'is_installment' => 'boolean',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_PARTIAL = 'partial';
    public const STATUS_PAID = 'paid';
    public const STATUS_WAIVED = 'waived';

    public static function statusOptions(): array
    {
        return [
            self::STATUS_PENDING => 'বাকি',
            self::STATUS_PARTIAL => 'আংশিক পরিশোধ',
            self::STATUS_PAID => 'পরিশোধিত',
            self::STATUS_WAIVED => 'মওকুফ',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function feeStructure(): BelongsTo
    {
        return $this->belongsTo(FeeStructure::class);
    }

    public function feeDiscount(): BelongsTo
    {
        return $this->belongsTo(FeeDiscount::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(FeePayment::class);
    }

    public function installments(): HasMany
    {
        return $this->hasMany(FeeInstallment::class)->orderBy('installment_no');
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(FeeRefund::class);
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
     * Update payment status based on paid amount
     */
    public function updateStatus(): void
    {
        if ($this->paid_amount >= $this->final_amount) {
            $this->status = self::STATUS_PAID;
            $this->due_amount = 0;
        } elseif ($this->paid_amount > 0) {
            $this->status = self::STATUS_PARTIAL;
            $this->due_amount = $this->final_amount - $this->paid_amount;
        }
        $this->save();
    }

    /**
     * Create installments for this fee
     */
    public function createInstallments(int $numberOfInstallments, array $dueDates = []): void
    {
        $installmentAmount = round($this->final_amount / $numberOfInstallments, 2);
        $remainder = $this->final_amount - ($installmentAmount * $numberOfInstallments);

        for ($i = 1; $i <= $numberOfInstallments; $i++) {
            $amount = $installmentAmount;
            if ($i === $numberOfInstallments) {
                $amount += $remainder; // Add remainder to last installment
            }

            $dueDate = $dueDates[$i - 1] ?? now()->addMonths($i - 1);

            FeeInstallment::create([
                'student_fee_id' => $this->id,
                'installment_no' => $i,
                'amount' => $amount,
                'due_date' => $dueDate,
                'status' => 'pending',
            ]);
        }

        $this->update([
            'is_installment' => true,
            'total_installments' => $numberOfInstallments,
        ]);
    }
}
