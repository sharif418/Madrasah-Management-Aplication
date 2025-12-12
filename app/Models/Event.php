<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'title',
        'description',
        'type',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'venue',
        'is_holiday',
        'is_public',
        'image',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_holiday' => 'boolean',
        'is_public' => 'boolean',
    ];

    public const TYPE_ACADEMIC = 'academic';
    public const TYPE_CULTURAL = 'cultural';
    public const TYPE_RELIGIOUS = 'religious';
    public const TYPE_SPORTS = 'sports';
    public const TYPE_MEETING = 'meeting';
    public const TYPE_OTHER = 'other';

    public static function typeOptions(): array
    {
        return [
            self::TYPE_ACADEMIC => 'শিক্ষা বিষয়ক',
            self::TYPE_CULTURAL => 'সাংস্কৃতিক',
            self::TYPE_RELIGIOUS => 'ধর্মীয়',
            self::TYPE_SPORTS => 'খেলাধুলা',
            self::TYPE_MEETING => 'সভা',
            self::TYPE_OTHER => 'অন্যান্য',
        ];
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>=', now()->toDateString())
            ->orderBy('start_date');
    }

    public function scopeHolidays($query)
    {
        return $query->where('is_holiday', true);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Check if event is ongoing
     */
    public function getIsOngoingAttribute(): bool
    {
        $today = now()->toDateString();
        return $this->start_date <= $today &&
            ($this->end_date >= $today || $this->end_date === null);
    }

    /**
     * Get formatted date range
     */
    public function getDateRangeAttribute(): string
    {
        if ($this->end_date && $this->start_date != $this->end_date) {
            return $this->start_date->format('d M') . ' - ' . $this->end_date->format('d M Y');
        }
        return $this->start_date->format('d M Y');
    }
}
