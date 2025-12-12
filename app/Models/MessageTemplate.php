<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageTemplate extends Model
{
    protected $fillable = [
        'name',
        'type',
        'subject',
        'content',
        'category',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function getTypeOptions(): array
    {
        return [
            'sms' => 'SMS',
            'email' => 'ইমেইল',
            'both' => 'উভয়',
        ];
    }

    public static function getCategoryOptions(): array
    {
        return [
            'fee' => 'ফি সংক্রান্ত',
            'attendance' => 'উপস্থিতি',
            'exam' => 'পরীক্ষা',
            'notice' => 'নোটিশ',
            'reminder' => 'রিমাইন্ডার',
            'emergency' => 'জরুরি',
            'other' => 'অন্যান্য',
        ];
    }

    // Parse placeholders in content
    public function parseContent(array $data): string
    {
        $content = $this->content;
        foreach ($data as $key => $value) {
            $content = str_replace('{' . $key . '}', $value, $content);
        }
        return $content;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForSms($query)
    {
        return $query->whereIn('type', ['sms', 'both']);
    }

    public function scopeForEmail($query)
    {
        return $query->whereIn('type', ['email', 'both']);
    }
}
