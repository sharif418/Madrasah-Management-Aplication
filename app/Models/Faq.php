<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    protected $fillable = [
        'question',
        'answer',
        'category',
        'order',
        'is_published',
    ];

    protected $casts = [
        'order' => 'integer',
        'is_published' => 'boolean',
    ];

    public static function categoryOptions(): array
    {
        return [
            'admission' => 'ভর্তি',
            'fee' => 'ফি',
            'academic' => 'শিক্ষা',
            'hostel' => 'হোস্টেল',
            'other' => 'অন্যান্য',
        ];
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true)->orderBy('order');
    }
}
