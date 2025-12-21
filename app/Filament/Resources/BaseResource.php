<?php

namespace App\Filament\Resources;

use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;

/**
 * Base Resource class with role-based access checking.
 * All resources should extend this class for proper access enforcement.
 * 
 * Access is determined by config/role-access.php
 */
abstract class BaseResource extends Resource
{
    /**
     * Check if the current user can access this resource.
     * This is used by Filament to show/hide navigation items.
     */
    public static function canAccess(): bool
    {
        $user = auth()->user();

        if (!$user) {
            return false;
        }

        // Get the resource name (e.g., TeacherResource -> Teacher)
        $resourceName = static::getResourceName();

        // Check if user's role has access to this resource
        return static::roleHasAccess($user, $resourceName);
    }

    /**
     * Check if user's role has access to the resource.
     */
    protected static function roleHasAccess($user, string $resourceName): bool
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

            $allowedResources = $roleAccess[$role];

            // If role has '*' (all access), allow everything
            if ($allowedResources === '*') {
                return true;
            }

            // Check if resource is in the allowed list
            if (is_array($allowedResources) && in_array($resourceName, $allowedResources)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the resource name without 'Resource' suffix.
     * e.g., TeacherResource -> Teacher
     */
    public static function getResourceName(): string
    {
        $className = class_basename(static::class);
        return str_replace('Resource', '', $className);
    }

    /**
     * Determine if the user can view any records.
     */
    public static function canViewAny(): bool
    {
        return static::canAccess();
    }

    /**
     * Determine if the user can create a record.
     */
    public static function canCreate(): bool
    {
        return static::canAccess();
    }

    /**
     * Determine if the user can view a record.
     */
    public static function canView(Model $record): bool
    {
        return static::canAccess();
    }

    /**
     * Determine if the user can edit a record.
     */
    public static function canEdit(Model $record): bool
    {
        return static::canAccess();
    }

    /**
     * Determine if the user can delete a record.
     */
    public static function canDelete(Model $record): bool
    {
        return static::canAccess();
    }
}
