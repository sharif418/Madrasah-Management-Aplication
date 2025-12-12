<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = [
        'name',
        'designation',
        'photo',
        'content',
        'rating',
        'is_published',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_published' => 'boolean',
    ];

    public static function designationOptions(): array
    {
        return [
            'student' => 'প্রাক্তন ছাত্র',
            'parent' => 'অভিভাবক',
            'teacher' => 'শিক্ষক',
            'other' => 'অন্যান্য',
        ];
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}
