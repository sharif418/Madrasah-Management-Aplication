<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MealMenu extends Model
{
    protected $fillable = [
        'hostel_id',
        'day_of_week',
        'meal_type',
        'menu_items',
        'is_active',
    ];

    protected $casts = [
        'menu_items' => 'array',
        'is_active' => 'boolean',
    ];

    // Day options
    public static function dayOptions(): array
    {
        return [
            'saturday' => 'শনিবার',
            'sunday' => 'রবিবার',
            'monday' => 'সোমবার',
            'tuesday' => 'মঙ্গলবার',
            'wednesday' => 'বুধবার',
            'thursday' => 'বৃহস্পতিবার',
            'friday' => 'শুক্রবার',
        ];
    }

    // Meal type options
    public static function mealTypeOptions(): array
    {
        return [
            'breakfast' => 'সকালের নাস্তা',
            'lunch' => 'দুপুরের খাবার',
            'snacks' => 'বিকালের নাস্তা',
            'dinner' => 'রাতের খাবার',
        ];
    }

    public function hostel()
    {
        return $this->belongsTo(Hostel::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForDay($query, $day)
    {
        return $query->where('day_of_week', $day);
    }
}
