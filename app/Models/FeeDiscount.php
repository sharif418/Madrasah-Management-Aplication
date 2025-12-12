<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * FeeDiscount Model
 * 
 * ফি ছাড় ব্যবস্থাপনা - এতিম, গরীব, মেধাবী, ভাই-বোন ছাড় ইত্যাদি
 * 
 * @property int $id
 * @property string $name ছাড়ের নাম
 * @property string $discount_type 'percentage' বা 'fixed'
 * @property float $amount ছাড়ের পরিমাণ (শতাংশ বা নির্দিষ্ট টাকা)
 * @property string|null $description বিবরণ
 * @property bool $is_active সক্রিয় কি না
 */
class FeeDiscount extends Model
{
    protected $fillable = [
        'name',
        'discount_type',
        'amount',
        'description',
        'is_active',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Discount Types
     */
    public const TYPE_PERCENTAGE = 'percentage';
    public const TYPE_FIXED = 'fixed';

    /**
     * Get discount type options for forms
     */
    public static function discountTypeOptions(): array
    {
        return [
            self::TYPE_PERCENTAGE => 'শতাংশ (%)',
            self::TYPE_FIXED => 'নির্দিষ্ট টাকা',
        ];
    }

    /**
     * Student Fees এ এই ছাড় প্রযোজ্য
     */
    public function studentFees(): HasMany
    {
        return $this->hasMany(StudentFee::class, 'fee_discount_id');
    }

    /**
     * Only active discounts
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Calculate actual discount amount based on original fee
     * 
     * @param float $originalAmount মূল ফি
     * @return float ছাড়ের পরিমাণ
     */
    public function calculateDiscount(float $originalAmount): float
    {
        if ($this->discount_type === self::TYPE_PERCENTAGE) {
            return round(($originalAmount * $this->amount) / 100, 2);
        }

        // Fixed amount - but cannot exceed original
        return min($this->amount, $originalAmount);
    }

    /**
     * Get formatted discount display
     */
    public function getFormattedDiscountAttribute(): string
    {
        if ($this->discount_type === self::TYPE_PERCENTAGE) {
            return $this->amount . '%';
        }

        return '৳' . number_format($this->amount, 2);
    }

    /**
     * Boot method for model events
     */
    protected static function boot()
    {
        parent::boot();

        // Validate discount amount before saving
        static::saving(function ($discount) {
            if ($discount->discount_type === self::TYPE_PERCENTAGE && $discount->amount > 100) {
                throw new \InvalidArgumentException('শতাংশ ছাড় ১০০% এর বেশি হতে পারে না।');
            }
        });
    }
}
