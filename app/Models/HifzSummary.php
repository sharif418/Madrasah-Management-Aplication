<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HifzSummary extends Model
{
    protected $table = 'hifz_summaries';

    protected $fillable = [
        'student_id',
        'total_para_completed',
        'current_para',
        'hifz_start_date',
        'hifz_complete_date',
        'is_hafiz',
        'status',
    ];

    protected $casts = [
        'total_para_completed' => 'integer',
        'current_para' => 'integer',
        'hifz_start_date' => 'date',
        'hifz_complete_date' => 'date',
        'is_hafiz' => 'boolean',
    ];

    public const STATUS_ONGOING = 'ongoing';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_PAUSED = 'paused';

    public static function statusOptions(): array
    {
        return [
            self::STATUS_ONGOING => 'চলমান',
            self::STATUS_COMPLETED => 'সম্পন্ন',
            self::STATUS_PAUSED => 'বিরতি',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function scopeOngoing($query)
    {
        return $query->where('status', self::STATUS_ONGOING);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Get progress percentage
     */
    public function getProgressPercentageAttribute(): float
    {
        return round(($this->total_para_completed / 30) * 100, 1);
    }

    /**
     * Mark as Hafiz when 30 para completed
     */
    public function checkCompletion(): void
    {
        if ($this->total_para_completed >= 30) {
            $this->is_hafiz = true;
            $this->status = self::STATUS_COMPLETED;
            $this->hifz_complete_date = now()->toDateString();
            $this->save();
        }
    }
}
