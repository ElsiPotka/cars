<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    // Vehicle permissions
    public const VIEW_VEHICLES = 'view vehicles';

    public const CREATE_VEHICLES = 'create vehicles';

    public const EDIT_VEHICLES = 'edit vehicles';

    public const DELETE_VEHICLES = 'delete vehicles';

    // User management permissions
    public const VIEW_USERS = 'view users';

    public const CREATE_USERS = 'create users';

    public const EDIT_USERS = 'edit users';

    public const DELETE_USERS = 'delete users';

    // Sales permissions
    public const VIEW_SALES = 'view sales';

    public const CREATE_SALES = 'create sales';

    public const EDIT_SALES = 'edit sales';

    public const DELETE_SALES = 'delete sales';

    // Reports permissions
    public const VIEW_REPORTS = 'view reports';

    public const EXPORT_REPORTS = 'export reports';

    // Settings permissions
    public const MANAGE_SETTINGS = 'manage settings';

    public const MANAGE_ROLES = 'manage roles';

    public const MANAGE_PERMISSIONS = 'manage permissions';

    /**
     * Get all defined permission constants.
     *
     * @return array<string>
     */
    public static function getDefinedPermissions(): array
    {
        return [
            // Vehicle permissions
            self::VIEW_VEHICLES,
            self::CREATE_VEHICLES,
            self::EDIT_VEHICLES,
            self::DELETE_VEHICLES,

            // User management permissions
            self::VIEW_USERS,
            self::CREATE_USERS,
            self::EDIT_USERS,
            self::DELETE_USERS,

            // Sales permissions
            self::VIEW_SALES,
            self::CREATE_SALES,
            self::EDIT_SALES,
            self::DELETE_SALES,

            // Reports permissions
            self::VIEW_REPORTS,
            self::EXPORT_REPORTS,

            // Settings permissions
            self::MANAGE_SETTINGS,
            self::MANAGE_ROLES,
            self::MANAGE_PERMISSIONS,
        ];
    }

    /**
     * Get permissions by category.
     *
     * @return array<string, array<string>>
     */
    public static function getPermissionsByCategory(): array
    {
        return [
            'vehicles' => [
                self::VIEW_VEHICLES,
                self::CREATE_VEHICLES,
                self::EDIT_VEHICLES,
                self::DELETE_VEHICLES,
            ],
            'users' => [
                self::VIEW_USERS,
                self::CREATE_USERS,
                self::EDIT_USERS,
                self::DELETE_USERS,
            ],
            'sales' => [
                self::VIEW_SALES,
                self::CREATE_SALES,
                self::EDIT_SALES,
                self::DELETE_SALES,
            ],
            'reports' => [
                self::VIEW_REPORTS,
                self::EXPORT_REPORTS,
            ],
            'settings' => [
                self::MANAGE_SETTINGS,
                self::MANAGE_ROLES,
                self::MANAGE_PERMISSIONS,
            ],
        ];
    }

    /**
     * Check if a permission name is valid.
     */
    public static function isValidPermission(string $permission): bool
    {
        return in_array($permission, self::getDefinedPermissions(), true);
    }
}
