<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'group',
        'key',
        'value',
        'type',
        'description',
    ];

    public const GROUP_GENERAL = 'general';
    public const GROUP_ACADEMIC = 'academic';
    public const GROUP_SMS = 'sms';
    public const GROUP_EMAIL = 'email';
    public const GROUP_PAYMENT = 'payment';

    public static function groupOptions(): array
    {
        return [
            self::GROUP_GENERAL => 'সাধারণ',
            self::GROUP_ACADEMIC => 'শিক্ষা',
            self::GROUP_SMS => 'SMS',
            self::GROUP_EMAIL => 'ইমেইল',
            self::GROUP_PAYMENT => 'পেমেন্ট',
        ];
    }

    public static function typeOptions(): array
    {
        return [
            'text' => 'টেক্সট',
            'textarea' => 'টেক্সটএরিয়া',
            'number' => 'সংখ্যা',
            'boolean' => 'হ্যাঁ/না',
            'json' => 'JSON',
            'file' => 'ফাইল',
        ];
    }

    /**
     * Get setting value by key
     */
    public static function getValue(string $key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Set setting value by key
     */
    public static function setValue(string $key, $value, string $group = 'general', string $type = 'text'): void
    {
        self::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group, 'type' => $type]
        );
    }

    /**
     * Get all settings as array
     */
    public static function getAllAsArray(): array
    {
        return self::pluck('value', 'key')->toArray();
    }
}
