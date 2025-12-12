<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LibraryMember extends Model
{
    protected $fillable = [
        'member_id',
        'member_type',
        'reference_id',
        'name',
        'phone',
        'max_books',
        'membership_date',
        'expiry_date',
        'status',
    ];

    protected $casts = [
        'max_books' => 'integer',
        'membership_date' => 'date',
        'expiry_date' => 'date',
    ];

    public const TYPE_STUDENT = 'student';
    public const TYPE_TEACHER = 'teacher';
    public const TYPE_STAFF = 'staff';
    public const TYPE_EXTERNAL = 'external';

    public const STATUS_ACTIVE = 'active';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_SUSPENDED = 'suspended';

    public static function memberTypeOptions(): array
    {
        return [
            self::TYPE_STUDENT => 'ছাত্র',
            self::TYPE_TEACHER => 'শিক্ষক',
            self::TYPE_STAFF => 'কর্মচারী',
            self::TYPE_EXTERNAL => 'বহিরাগত',
        ];
    }

    public static function statusOptions(): array
    {
        return [
            self::STATUS_ACTIVE => 'সক্রিয়',
            self::STATUS_EXPIRED => 'মেয়াদোত্তীর্ণ',
            self::STATUS_SUSPENDED => 'স্থগিত',
        ];
    }

    public function issues(): HasMany
    {
        return $this->hasMany(BookIssue::class);
    }

    public function activeIssues(): HasMany
    {
        return $this->hasMany(BookIssue::class)->where('status', 'issued');
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Check if member can borrow more books
     */
    public function canBorrowMore(): bool
    {
        return $this->activeIssues()->count() < $this->max_books;
    }

    /**
     * Generate unique member ID
     */
    public static function generateMemberId(string $type): string
    {
        $prefix = match ($type) {
            'student' => 'STU',
            'teacher' => 'TCH',
            'staff' => 'STF',
            default => 'EXT',
        };

        $year = date('Y');
        $lastMember = self::where('member_type', $type)
            ->whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = $lastMember ? ((int) substr($lastMember->member_id, -4) + 1) : 1;

        return $prefix . '-' . $year . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($member) {
            if (empty($member->member_id)) {
                $member->member_id = self::generateMemberId($member->member_type);
            }
        });
    }
}
