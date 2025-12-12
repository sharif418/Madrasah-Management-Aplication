<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmergencyAlert extends Model
{
    protected $fillable = [
        'title',
        'message',
        'severity',
        'target',
        'send_sms',
        'send_email',
        'is_active',
        'expires_at',
        'created_by',
    ];

    protected $casts = [
        'send_sms' => 'boolean',
        'send_email' => 'boolean',
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->created_by && auth()->check()) {
                $model->created_by = auth()->id();
            }
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function getSeverityOptions(): array
    {
        return [
            'info' => 'তথ্য',
            'warning' => 'সতর্কতা',
            'critical' => 'জরুরি',
        ];
    }

    public static function getTargetOptions(): array
    {
        return [
            'all' => 'সকল',
            'students' => 'ছাত্র',
            'teachers' => 'শিক্ষক',
            'parents' => 'অভিভাবক',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
}
