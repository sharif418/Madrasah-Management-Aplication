<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    protected $fillable = [
        'title',
        'title_en',
        'category_id',
        'author',
        'publisher',
        'isbn',
        'publish_year',
        'edition',
        'language',
        'total_copies',
        'available_copies',
        'shelf_location',
        'price',
        'cover_image',
        'description',
        'is_available',
    ];

    protected $casts = [
        'total_copies' => 'integer',
        'available_copies' => 'integer',
        'price' => 'decimal:2',
        'is_available' => 'boolean',
    ];

    public static function languageOptions(): array
    {
        return [
            'বাংলা' => 'বাংলা',
            'আরবি' => 'আরবি',
            'English' => 'ইংরেজি',
            'উর্দু' => 'উর্দু',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BookCategory::class, 'category_id');
    }

    public function issues(): HasMany
    {
        return $this->hasMany(BookIssue::class);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)->where('available_copies', '>', 0);
    }

    /**
     * Reduce available copies when book is issued
     */
    public function reduceAvailableCopies(): void
    {
        if ($this->available_copies > 0) {
            $this->available_copies--;
            $this->is_available = $this->available_copies > 0;
            $this->save();
        }
    }

    /**
     * Increase available copies when book is returned
     */
    public function increaseAvailableCopies(): void
    {
        if ($this->available_copies < $this->total_copies) {
            $this->available_copies++;
            $this->is_available = true;
            $this->save();
        }
    }
}
