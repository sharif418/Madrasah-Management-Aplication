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

        // Get the permission name based on resource name
        // e.g., TeacherResource -> view_any_teacher
        $permissionName = static::getViewAnyPermission();

        return $user->can($permissionName);
    }

    /**
     * Get the view_any permission name for this resource.
     */
    public static function getViewAnyPermission(): string
    {
        // Convert resource class name to permission name
        // e.g., TeacherResource -> teacher
        // e.g., AcademicYearResource -> academic::year
        $resourceName = class_basename(static::class);
        $resourceName = str_replace('Resource', '', $resourceName);

        // Convert CamelCase to snake_case with :: separator
        $permissionName = strtolower(preg_replace('/([a-z])([A-Z])/', '$1::$2', $resourceName));

        return 'view_any_' . $permissionName;
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
        $user = auth()->user();

        if (!$user) {
            return false;
        }

        if ($user->hasRole('super_admin')) {
            return true;
        }

        $resourceName = class_basename(static::class);
        $resourceName = str_replace('Resource', '', $resourceName);
        $permissionName = strtolower(preg_replace('/([a-z])([A-Z])/', '$1::$2', $resourceName));

        return $user->can('create_' . $permissionName);
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

        $resourceName = class_basename(static::class);
        $resourceName = str_replace('Resource', '', $resourceName);
        $permissionName = strtolower(preg_replace('/([a-z])([A-Z])/', '$1::$2', $resourceName));

        return $user->can('update_' . $permissionName);
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

        $resourceName = class_basename(static::class);
        $resourceName = str_replace('Resource', '', $resourceName);
        $permissionName = strtolower(preg_replace('/([a-z])([A-Z])/', '$1::$2', $resourceName));

        return $user->can('delete_' . $permissionName);
    }
}
