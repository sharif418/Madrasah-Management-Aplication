<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeeRefund extends Model
{
    use HasFactory;

    protected $fillable = [
        'refund_no',
        'student_id',
        'fee_payment_id',
        'student_fee_id',
        'refund_amount',
        'reason',
        'refund_method',
        'transaction_id',
        'status',
        'requested_by',
        'approved_by',
        'approved_at',
        'refund_date',
        'rejection_reason',
    ];

    protected $casts = [
        'refund_amount' => 'decimal:2',
        'approved_at' => 'datetime',
        'refund_date' => 'date',
    ];

    // Boot
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($refund) {
            if (!$refund->refund_no) {
                $refund->refund_no = self::generateRefundNo();
            }
        });
    }

    // Relationships
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function feePayment(): BelongsTo
    {
        return $this->belongsTo(FeePayment::class);
    }

    public function studentFee(): BelongsTo
    {
        return $this->belongsTo(StudentFee::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    // Methods
    public static function generateRefundNo(): string
    {
        $year = now()->year;
        $lastRefund = self::whereYear('created_at', $year)->orderBy('id', 'desc')->first();
        $nextNumber = $lastRefund ? (int) substr($lastRefund->refund_no, -4) + 1 : 1;

        return sprintf('RF-%d-%04d', $year, $nextNumber);
    }

    public function approve(int $approvedBy): void
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $approvedBy,
            'approved_at' => now(),
        ]);
    }

    public function complete(?string $transactionId = null): void
    {
        $this->update([
            'status' => 'completed',
            'refund_date' => now(),
            'transaction_id' => $transactionId,
        ]);
    }

    public function reject(string $reason, int $rejectedBy): void
    {
        $this->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'approved_by' => $rejectedBy,
            'approved_at' => now(),
        ]);
    }

    public static function getStatusOptions(): array
    {
        return [
            'pending' => 'অপেক্ষমাণ',
            'approved' => 'অনুমোদিত',
            'completed' => 'সম্পন্ন',
            'rejected' => 'বাতিল',
        ];
    }

    public static function getMethodOptions(): array
    {
        return [
            'cash' => 'নগদ',
            'bkash' => 'বিকাশ',
            'nagad' => 'নগদ (Nagad)',
            'bank' => 'ব্যাংক',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::getStatusOptions()[$this->status] ?? $this->status;
    }

    public function getMethodLabelAttribute(): string
    {
        return self::getMethodOptions()[$this->refund_method] ?? $this->refund_method;
    }
}
