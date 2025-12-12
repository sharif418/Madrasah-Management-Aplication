<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Circular extends Model
{
    protected $fillable = [
        'circular_no',
        'title',
        'content',
        'target_audience',
        'issue_date',
        'effective_date',
        'priority',
        'status',
        'attachment',
        'created_by',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'effective_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->circular_no) {
                $model->circular_no = self::generateCircularNo();
            }
            if (!$model->created_by && auth()->check()) {
                $model->created_by = auth()->id();
            }
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function generateCircularNo(): string
    {
        $year = now()->year;
        $last = self::whereYear('created_at', $year)->orderBy('id', 'desc')->first();
        $next = $last ? (int) substr($last->circular_no, -4) + 1 : 1;
        return sprintf('CIR-%d-%04d', $year, $next);
    }

    public static function getAudienceOptions(): array
    {
        return [
            'all' => 'সকলের জন্য',
            'students' => 'ছাত্রদের জন্য',
            'teachers' => 'শিক্ষকদের জন্য',
            'staff' => 'কর্মচারীদের জন্য',
            'parents' => 'অভিভাবকদের জন্য',
        ];
    }

    public static function getPriorityOptions(): array
    {
        return [
            'normal' => 'সাধারণ',
            'important' => 'গুরুত্বপূর্ণ',
            'urgent' => 'জরুরি',
        ];
    }

    public static function getStatusOptions(): array
    {
        return [
            'draft' => 'ড্রাফট',
            'published' => 'প্রকাশিত',
            'archived' => 'আর্কাইভ',
        ];
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}
