<?php

use App\Models\Company;
use App\Models\EmployeeJobPosition;
use App\Models\Role;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\PermissionRegistrar;

beforeEach(function () {
    app()[PermissionRegistrar::class]->forgetCachedPermissions();
});

test('admin can create company', function () {
    $admin = User::factory()->create();
    $role = Role::create(['name' => 'admin', 'guard_name' => 'web']);
    $admin->assignRole($role);

    Sanctum::actingAs($admin);

    $response = $this->postJson('/api/companies', [
        'name' => 'Test Company',
        'address' => '123 Test St',
        'phone' => '123-456-7890',
        'email' => 'test@example.com',
        'website' => 'https://example.com',
    ]);

    $response->assertCreated();
    $this->assertDatabaseHas('companies', ['name' => 'Test Company']);
});

test('manager can create company', function () {
    $manager = User::factory()->create();
    $role = Role::create(['name' => 'manager', 'guard_name' => 'web']);
    $manager->assignRole($role);

    Sanctum::actingAs($manager);

    $response = $this->postJson('/api/companies', [
        'name' => 'Manager Company',
    ]);

    $response->assertCreated();
    $this->assertDatabaseHas('companies', ['name' => 'Manager Company']);
});

test('customer cannot create company', function () {
    $customer = User::factory()->create();
    $role = Role::create(['name' => 'customer', 'guard_name' => 'web']);
    $customer->assignRole($role);

    Sanctum::actingAs($customer);

    $response = $this->postJson('/api/companies', [
        'name' => 'Customer Company',
    ]);

    $response->assertForbidden();
    $this->assertDatabaseMissing('companies', ['name' => 'Customer Company']);
});

test('admin can add employee to company', function () {
    $admin = User::factory()->create();
    $role = Role::where('name', 'admin')->first(); // Re-use role if exists from previous test
    if (!$role) {
        $role = Role::create(['name' => 'admin', 'guard_name' => 'web']);
    }
    $admin->assignRole($role);

    Sanctum::actingAs($admin);

    $company = Company::create(['name' => 'Test Company']);
    $userToAdd = User::factory()->create();
    $employeeRole = Role::firstOrCreate(['name' => 'employee', 'guard_name' => 'web']);
    $userToAdd->assignRole($employeeRole);

    $response = $this->postJson("/api/companies/{$company->id}/employees", [
        'user_id' => $userToAdd->id,
        'role_id' => $employeeRole->id,
    ]);

    $response->assertCreated();
    $this->assertDatabaseHas('employee_job_positions', [
        'company_id' => $company->id,
        'user_id' => $userToAdd->id,
        'role_id' => $employeeRole->id,
    ]);
});

test('admin can update company', function () {
    $admin = User::factory()->create();
    $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    $admin->assignRole($role);
    Sanctum::actingAs($admin);

    $company = Company::create(['name' => 'Old Name']);

    $response = $this->putJson("/api/companies/{$company->id}", [
        'name' => 'New Name',
    ]);

    $response->assertOk();
    $this->assertDatabaseHas('companies', ['id' => $company->id, 'name' => 'New Name']);
});

test('admin can delete company', function () {
    $admin = User::factory()->create();
    $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    $admin->assignRole($role);
    Sanctum::actingAs($admin);

    $company = Company::create(['name' => 'To Delete']);

    $response = $this->deleteJson("/api/companies/{$company->id}");

    $response->assertOk();
    $this->assertSoftDeleted($company);
});

test('admin can remove employee from company', function () {
    $admin = User::factory()->create();
    $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    $admin->assignRole($role);
    Sanctum::actingAs($admin);

    $company = Company::create(['name' => 'Test Company']);
    $employee = User::factory()->create();
    $roleUser = Role::firstOrCreate(['name' => 'sales_agent', 'guard_name' => 'web']);
    
    EmployeeJobPosition::create([
        'company_id' => $company->id,
        'user_id' => $employee->id,
        'role_id' => $roleUser->id,
    ]);

    $response = $this->deleteJson("/api/companies/{$company->id}/employees/{$employee->id}");

    $response->assertOk();
    $this->assertSoftDeleted('employee_job_positions', [
        'company_id' => $company->id,
        'user_id' => $employee->id,
    ]);
});

test('cannot add employee with role they do not have', function () {
    $admin = User::factory()->create();
    $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    $admin->assignRole($role);
    Sanctum::actingAs($admin);

    $company = Company::create(['name' => 'Test Company']);
    $userToAdd = User::factory()->create();
    $roleNotAssigned = Role::create(['name' => 'manager', 'guard_name' => 'web']); // User does not have this role

    $response = $this->postJson("/api/companies/{$company->id}/employees", [
        'user_id' => $userToAdd->id,
        'role_id' => $roleNotAssigned->id,
    ]);

    $response->assertInvalid(['role_id']);
});
