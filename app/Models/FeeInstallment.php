<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeeInstallment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_fee_id',
        'installment_no',
        'amount',
        'due_date',
        'paid_amount',
        'paid_date',
        'status',
        'collected_by',
        'remarks',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_date' => 'date',
        'paid_date' => 'date',
    ];

    // Relationships
    public function studentFee(): BelongsTo
    {
        return $this->belongsTo(StudentFee::class);
    }

    public function collector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'collected_by');
    }

    // Accessors
    public function getDueAmountAttribute(): float
    {
        return max(0, $this->amount - $this->paid_amount);
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->status !== 'paid' && $this->due_date < now();
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue')
            ->orWhere(fn($q) => $q->where('status', 'pending')->where('due_date', '<', now()));
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    // Methods
    public function markAsPaid(float $amount, ?int $collectedBy = null): void
    {
        $this->paid_amount = $amount;
        $this->paid_date = now();
        $this->collected_by = $collectedBy;
        $this->status = $amount >= $this->amount ? 'paid' : 'partial';
        $this->save();
    }

    public static function getStatusOptions(): array
    {
        return [
            'pending' => 'বকেয়া',
            'paid' => 'পরিশোধিত',
            'partial' => 'আংশিক',
            'overdue' => 'মেয়াদোত্তীর্ণ',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::getStatusOptions()[$this->status] ?? $this->status;
    }
}
