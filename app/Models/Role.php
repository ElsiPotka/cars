<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    // Role constants
    public const string ADMIN = 'admin';

    public const string MANAGER = 'manager';

    public const string SALES_AGENT = 'sales_agent';

    public const string CUSTOMER = 'customer';

    /**
     * Get all available role names.
     *
     * @return array<string>
     */
    public static function getAllRoles(): array
    {
        return [
            self::ADMIN,
            self::MANAGER,
            self::SALES_AGENT,
            self::CUSTOMER,
        ];
    }

    /**
     * Check if a role name is valid.
     */
    public static function isValidRole(string $role): bool
    {
        return in_array($role, self::getAllRoles(), true);
    }
}
