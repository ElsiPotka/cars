<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        foreach (Permission::getDefinedPermissions() as $permission) {
            Permission::create(['name' => $permission]);
        }

        $adminRole = Role::create(['name' => Role::ADMIN]);
        $managerRole = Role::create(['name' => Role::MANAGER]);
        $salesAgentRole = Role::create(['name' => Role::SALES_AGENT]);
        $customerRole = Role::create(['name' => Role::CUSTOMER]);

        $adminRole->givePermissionTo(Permission::all());

        $managerRole->givePermissionTo([
            Permission::VIEW_VEHICLES,
            Permission::CREATE_VEHICLES,
            Permission::EDIT_VEHICLES,
            Permission::VIEW_USERS,
            Permission::VIEW_SALES,
            Permission::CREATE_SALES,
            Permission::EDIT_SALES,
            Permission::VIEW_REPORTS,
            Permission::EXPORT_REPORTS,
        ]);

        $salesAgentRole->givePermissionTo([
            Permission::VIEW_VEHICLES,
            Permission::VIEW_SALES,
            Permission::CREATE_SALES,
            Permission::VIEW_REPORTS,
        ]);

        $customerRole->givePermissionTo([
            Permission::VIEW_VEHICLES,
        ]);
    }
}
