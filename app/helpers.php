<?php

use App\Models\Setting;

if (!function_exists('setting')) {
    /**
     * Get or set a setting value
     *
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    function setting(?string $key = null, $default = null)
    {
        if ($key === null) {
            return Setting::getAllAsArray();
        }

        return Setting::getValue($key, $default);
    }
}

if (!function_exists('institution_name')) {
    /**
     * Get institution name
     */
    function institution_name(): string
    {
        return setting('institution_name', 'মাদরাসা ম্যানেজমেন্ট সিস্টেম');
    }
}

if (!function_exists('institution_slogan')) {
    /**
     * Get institution slogan
     */
    function institution_slogan(): string
    {
        return setting('institution_slogan', 'কুরআন ও সুন্নাহর আলোকে দ্বীনি ও আধুনিক শিক্ষার সমন্বয়');
    }
}

if (!function_exists('institution_logo')) {
    /**
     * Get institution logo URL
     */
    function institution_logo(): string
    {
        $logo = setting('logo');
        return $logo ? asset('storage/' . $logo) : asset('images/default-logo.png');
    }
}

if (!function_exists('institution_address')) {
    /**
     * Get institution address
     */
    function institution_address(): string
    {
        return setting('address', '');
    }
}

if (!function_exists('institution_phone')) {
    /**
     * Get institution phone
     */
    function institution_phone(): string
    {
        return setting('phone', '');
    }
}

if (!function_exists('institution_email')) {
    /**
     * Get institution email
     */
    function institution_email(): string
    {
        return setting('email', '');
    }
}

if (!function_exists('passing_marks')) {
    /**
     * Get passing marks percentage
     */
    function passing_marks(): int
    {
        return (int) setting('passing_marks', 33);
    }
}

if (!function_exists('late_fee_percent')) {
    /**
     * Get late fee percentage
     */
    function late_fee_percent(): float
    {
        return (float) setting('late_fee_percent', 5);
    }
}

if (!function_exists('sms_enabled')) {
    /**
     * Check if SMS is enabled
     */
    function sms_enabled(): bool
    {
        return (bool) setting('sms_enabled', false);
    }
}

if (!function_exists('bkash_enabled')) {
    /**
     * Check if bKash is enabled
     */
    function bkash_enabled(): bool
    {
        return (bool) setting('bkash_enabled', false);
    }
}

if (!function_exists('institution_start_time')) {
    /**
     * Get institution start time
     */
    function institution_start_time(): string
    {
        return setting('institution_start_time', '08:00');
    }
}

if (!function_exists('institution_end_time')) {
    /**
     * Get institution end time
     */
    function institution_end_time(): string
    {
        return setting('institution_end_time', '16:00');
    }
}
