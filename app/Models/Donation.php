<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Donation extends Model
{
    protected $fillable = [
        'donor_name',
        'donor_phone',
        'donor_email',
        'donor_address',
        'fund_type',
        'amount',
        'date',
        'payment_method',
        'transaction_id',
        'purpose',
        'receipt_no',
        'remarks',
        'received_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
    ];

    public const FUND_GENERAL = 'general';
    public const FUND_ZAKAT = 'zakat';
    public const FUND_SADAQAH = 'sadaqah';
    public const FUND_LILLAH = 'lillah';
    public const FUND_BUILDING = 'building';
    public const FUND_SCHOLARSHIP = 'scholarship';
    public const FUND_OTHER = 'other';

    public static function fundTypeOptions(): array
    {
        return [
            self::FUND_GENERAL => 'সাধারণ',
            self::FUND_ZAKAT => 'যাকাত',
            self::FUND_SADAQAH => 'সাদাকা',
            self::FUND_LILLAH => 'লিল্লাহ',
            self::FUND_BUILDING => 'ভবন নির্মাণ',
            self::FUND_SCHOLARSHIP => 'বৃত্তি',
            self::FUND_OTHER => 'অন্যান্য',
        ];
    }

    public static function paymentMethodOptions(): array
    {
        return [
            'cash' => 'নগদ',
            'bank' => 'ব্যাংক',
            'bkash' => 'বিকাশ',
            'nagad' => 'নগদ (Nagad)',
            'check' => 'চেক',
        ];
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
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

        $nextNumber = $lastReceipt ? ((int) substr($lastReceipt->receipt_no ?? '0', -6) + 1) : 1;

        return 'DON-' . $year . '-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($donation) {
            if (empty($donation->receipt_no)) {
                $donation->receipt_no = self::generateReceiptNo();
            }
        });
    }
}
