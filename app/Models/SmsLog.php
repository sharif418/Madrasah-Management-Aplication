<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmsLog extends Model
{
    protected $fillable = [
        'phone',
        'message',
        'type',
        'purpose',
        'gateway_response',
        'status',
        'sent_by',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_SENT = 'sent';
    public const STATUS_FAILED = 'failed';

    public static function statusOptions(): array
    {
        return [
            self::STATUS_PENDING => 'অপেক্ষমাণ',
            self::STATUS_SENT => 'প্রেরিত',
            self::STATUS_FAILED => 'ব্যর্থ',
        ];
    }

    public static function typeOptions(): array
    {
        return [
            'single' => 'একক',
            'bulk' => 'বাল্ক',
        ];
    }

    public static function purposeOptions(): array
    {
        return [
            'attendance' => 'উপস্থিতি',
            'fee_reminder' => 'ফি রিমাইন্ডার',
            'result' => 'ফলাফল',
            'notice' => 'নোটিশ',
            'emergency' => 'জরুরি',
            'other' => 'অন্যান্য',
        ];
    }

    public function sentBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    public function scopeSent($query)
    {
        return $query->where('status', self::STATUS_SENT);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Mark as sent
     */
    public function markAsSent(string $response = null): void
    {
        $this->status = self::STATUS_SENT;
        $this->gateway_response = $response;
        $this->save();
    }

    /**
     * Mark as failed
     */
    public function markAsFailed(string $response = null): void
    {
        $this->status = self::STATUS_FAILED;
        $this->gateway_response = $response;
        $this->save();
    }
}
