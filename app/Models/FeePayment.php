<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeePayment extends Model
{
    protected $fillable = [
        'student_fee_id',
        'student_id',
        'receipt_no',
        'amount',
        'late_fee',
        'total_amount',
        'payment_method',
        'transaction_id',
        'payment_date',
        'collected_by',
        'remarks',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'late_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public const METHOD_CASH = 'cash';
    public const METHOD_BKASH = 'bkash';
    public const METHOD_NAGAD = 'nagad';
    public const METHOD_ROCKET = 'rocket';
    public const METHOD_BANK = 'bank';
    public const METHOD_CHECK = 'check';

    public static function paymentMethodOptions(): array
    {
        return [
            self::METHOD_CASH => 'নগদ',
            self::METHOD_BKASH => 'বিকাশ',
            self::METHOD_NAGAD => 'নগদ (Nagad)',
            self::METHOD_ROCKET => 'রকেট',
            self::METHOD_BANK => 'ব্যাংক ট্রান্সফার',
            self::METHOD_CHECK => 'চেক',
        ];
    }

    public function studentFee(): BelongsTo
    {
        return $this->belongsTo(StudentFee::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function collectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'collected_by');
    }

    /**
     * Generate unique receipt number
     */
    public static function generateReceiptNo(): string
    {
        $year = date('Y');
        $lastReceipt = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = $lastReceipt ? ((int) substr($lastReceipt->receipt_no, -6) + 1) : 1;

        return 'RCP-' . $year . '-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            if (empty($payment->receipt_no)) {
                $payment->receipt_no = self::generateReceiptNo();
            }
            if (empty($payment->payment_date)) {
                $payment->payment_date = now();
            }
            if (empty($payment->total_amount)) {
                $payment->total_amount = $payment->amount + ($payment->late_fee ?? 0);
            }
        });

        static::created(function ($payment) {
            // Update student fee paid amount and status
            $studentFee = $payment->studentFee;
            if ($studentFee) {
                $studentFee->paid_amount += $payment->amount;
                $studentFee->updateStatus();
            }
        });
    }
}
