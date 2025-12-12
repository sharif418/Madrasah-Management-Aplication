<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kitab extends Model
{
    protected $table = 'kitab_list';

    protected $fillable = [
        'name',
        'name_en',
        'author',
        'class_id',
        'total_chapters',
        'total_lessons',
        'description',
        'is_active',
    ];

    protected $casts = [
        'total_chapters' => 'integer',
        'total_lessons' => 'integer',
        'is_active' => 'boolean',
    ];

    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassName::class, 'class_id');
    }

    public function progress(): HasMany
    {
        return $this->hasMany(KitabProgress::class, 'kitab_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
