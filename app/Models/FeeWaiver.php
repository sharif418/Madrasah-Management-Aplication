<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

/**
 * FeeWaiver Model
 * 
 * ফি মওকুফ ব্যবস্থাপনা - বিশেষ পরিস্থিতিতে ছাত্রের ফি মওকুফ করা
 * 
 * @property int $id
 * @property int $student_id ছাত্র
 * @property int $student_fee_id কোন ফি মওকুফ
 * @property float $waiver_amount মওকুফের পরিমাণ
 * @property string $reason মওকুফের কারণ
 * @property int|null $approved_by অনুমোদনকারী
 * @property \DateTime|null $approved_at অনুমোদনের সময়
 */
class FeeWaiver extends Model
{
    protected $fillable = [
        'student_id',
        'student_fee_id',
        'waiver_amount',
        'reason',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'waiver_amount' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    /**
     * Waiver এই ছাত্রের
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Waiver এই স্টুডেন্ট ফি এর জন্য
     */
    public function studentFee(): BelongsTo
    {
        return $this->belongsTo(StudentFee::class);
    }

    /**
     * কে অনুমোদন করেছে
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * অনুমোদিত কি না
     */
    public function getIsApprovedAttribute(): bool
    {
        return $this->approved_at !== null;
    }

    /**
     * শুধু অনুমোদিত waivers
     */
    public function scopeApproved($query)
    {
        return $query->whereNotNull('approved_at');
    }

    /**
     * শুধু pending (অপেক্ষমাণ) waivers
     */
    public function scopePending($query)
    {
        return $query->whereNull('approved_at');
    }

    /**
     * Waiver অনুমোদন করা
     */
    public function approve(?int $userId = null): bool
    {
        $this->approved_by = $userId ?? Auth::id();
        $this->approved_at = now();
        $saved = $this->save();

        if ($saved) {
            // Update the student fee status to waived
            $this->applyWaiverToStudentFee();
        }

        return $saved;
    }

    /**
     * Waiver প্রত্যাখ্যান করা (delete করে)
     */
    public function reject(): bool
    {
        return $this->delete();
    }

    /**
     * StudentFee এ waiver প্রয়োগ করা
     */
    protected function applyWaiverToStudentFee(): void
    {
        $studentFee = $this->studentFee;

        if (!$studentFee) {
            return;
        }

        // Calculate new final amount
        $newFinalAmount = max(0, $studentFee->final_amount - $this->waiver_amount);

        // If waiver covers entire amount or remaining due
        if ($newFinalAmount <= 0 || $this->waiver_amount >= $studentFee->due_amount) {
            $studentFee->status = StudentFee::STATUS_WAIVED;
            $studentFee->due_amount = 0;
        } else {
            $studentFee->due_amount = max(0, $studentFee->due_amount - $this->waiver_amount);
        }

        $studentFee->final_amount = $newFinalAmount;
        $studentFee->save();
    }

    /**
     * Boot method for model events
     */
    protected static function boot()
    {
        parent::boot();

        // Validate waiver amount before saving
        static::saving(function ($waiver) {
            if ($waiver->studentFee && $waiver->waiver_amount > $waiver->studentFee->final_amount) {
                throw new \InvalidArgumentException('মওকুফের পরিমাণ ফি এর পরিমাণের চেয়ে বেশি হতে পারে না।');
            }
        });
    }
}
