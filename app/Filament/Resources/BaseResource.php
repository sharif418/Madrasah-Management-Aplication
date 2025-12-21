<?php

namespace App\Filament\Resources;

use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;

/**
 * Base Resource class with permission checking.
 * All resources should extend this class for proper permission enforcement.
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

        // Super admin can access everything
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Check if user has ANY permission for this resource
        return static::hasAnyResourcePermission($user);
    }

    /**
     * Check if user has any permission related to this resource.
     */
    public static function hasAnyResourcePermission($user): bool
    {
        $resourceName = static::getResourcePermissionName();

        // Check for any of these permission types
        $permissionTypes = ['view', 'view_any', 'create', 'update', 'delete', 'delete_any'];

        foreach ($permissionTypes as $type) {
            if ($user->can($type . '_' . $resourceName)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the permission name suffix for this resource.
     * e.g., TeacherResource -> teacher
     * e.g., AcademicYearResource -> academic::year
     */
    public static function getResourcePermissionName(): string
    {
        $resourceName = class_basename(static::class);
        $resourceName = str_replace('Resource', '', $resourceName);

        // Convert CamelCase to lowercase with :: separator
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1::$2', $resourceName));
    }

    /**
     * Get the view_any permission name for this resource.
     */
    public static function getViewAnyPermission(): string
    {
        return 'view_any_' . static::getResourcePermissionName();
    }

    /**
     * Determine if the user can view any records.
     */
    public static function canViewAny(): bool
    {
        $user = auth()->user();

        if (!$user) {
            return false;
        }

        if ($user->hasRole('super_admin')) {
            return true;
        }

        $resourceName = static::getResourcePermissionName();

        // Allow view if user has view_any OR view permission
        return $user->can('view_any_' . $resourceName) || $user->can('view_' . $resourceName);
    }

    /**
     * Determine if the user can create a record.
     */
    public static function canCreate(): bool
    {
        $user = auth()->user();

        if (!$user) {
            return false;
        }

        if ($user->hasRole('super_admin')) {
            return true;
        }

        $resourceName = static::getResourcePermissionName();
        return $user->can('create_' . $resourceName);
    }

    /**
     * Determine if the user can edit a record.
     */
    public static function canEdit(Model $record): bool
    {
        $user = auth()->user();

        if (!$user) {
            return false;
        }

        if ($user->hasRole('super_admin')) {
            return true;
        }

        $resourceName = static::getResourcePermissionName();
        return $user->can('update_' . $resourceName);
    }

    /**
     * Determine if the user can delete a record.
     */
    public static function canDelete(Model $record): bool
    {
        $user = auth()->user();

        if (!$user) {
            return false;
        }

        if ($user->hasRole('super_admin')) {
            return true;
        }

        $resourceName = static::getResourcePermissionName();
        return $user->can('delete_' . $resourceName);
    }
}
