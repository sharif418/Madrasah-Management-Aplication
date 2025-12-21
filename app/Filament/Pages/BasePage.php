<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

/**
 * Base Page class with role-based access checking.
 * All custom pages should extend this class for proper access enforcement.
 * 
 * Access is determined by config/roleaccess.php
 */
abstract class BasePage extends Page
{
    /**
     * Check if the current user can access this page.
     */
    public static function canAccess(): bool
    {
        $user = auth()->user();

        if (!$user) {
            return false;
        }

        // Get the page name (e.g., AttendanceReport)
        $pageName = static::getPageName();

        // Check if user's role has access to this page
        return static::roleHasAccess($user, $pageName);
    }

    /**
     * Check if user's role has access to the page.
     */
    protected static function roleHasAccess($user, string $pageName): bool
    {
        // Get role access config
        $roleAccess = config('roleaccess', []);

        // Get user's roles
        $userRoles = $user->getRoleNames()->toArray();

        foreach ($userRoles as $role) {
            // Check if role exists in config
            if (!isset($roleAccess[$role])) {
                continue;
            }

            $allowedItems = $roleAccess[$role];

            // If role has '*' (all access), allow everything
            if ($allowedItems === '*') {
                return true;
            }

            // Check if page is in the allowed list (pages use full name)
            if (is_array($allowedItems) && in_array($pageName, $allowedItems)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the page name.
     * e.g., AttendanceReport
     */
    public static function getPageName(): string
    {
        return class_basename(static::class);
    }
}
