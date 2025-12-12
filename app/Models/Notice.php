<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notice extends Model
{
    protected $fillable = [
        'title',
        'content',
        'type',
        'audience',
        'class_id',
        'publish_date',
        'expiry_date',
        'is_published',
        'attachment',
        'created_by',
    ];

    protected $casts = [
        'publish_date' => 'date',
        'expiry_date' => 'date',
        'is_published' => 'boolean',
    ];

    public const TYPE_GENERAL = 'general';
    public const TYPE_ACADEMIC = 'academic';
    public const TYPE_EXAM = 'exam';
    public const TYPE_EVENT = 'event';
    public const TYPE_URGENT = 'urgent';

    public static function typeOptions(): array
    {
        return [
            self::TYPE_GENERAL => 'সাধারণ',
            self::TYPE_ACADEMIC => 'শিক্ষা বিষয়ক',
            self::TYPE_EXAM => 'পরীক্ষা',
            self::TYPE_EVENT => 'ইভেন্ট',
            self::TYPE_URGENT => 'জরুরি',
        ];
    }

    public static function audienceOptions(): array
    {
        return [
            'all' => 'সকলের জন্য',
            'students' => 'ছাত্রদের জন্য',
            'teachers' => 'শিক্ষকদের জন্য',
            'parents' => 'অভিভাবকদের জন্য',
            'staff' => 'স্টাফদের জন্য',
        ];
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassName::class, 'class_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true)
            ->where('publish_date', '<=', now())
            ->where(function ($q) {
                $q->whereNull('expiry_date')
                    ->orWhere('expiry_date', '>=', now());
            });
    }

    public function scopeUrgent($query)
    {
        return $query->where('type', self::TYPE_URGENT);
    }

    /**
     * Check if notice is active
     */
    public function getIsActiveAttribute(): bool
    {
        if (!$this->is_published)
            return false;
        if ($this->publish_date > now())
            return false;
        if ($this->expiry_date && $this->expiry_date < now())
            return false;
        return true;
    }
}
