<?php

use App\Models\Company;
use App\Models\EmployeeJobPosition;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('can create company', function () {
    $company = Company::create([
        'name' => 'Acme Corp',
        'address' => '123 Acme St',
        'phone' => '123-456-7890',
        'email' => 'info@acme.com',
        'website' => 'https://acme.com',
    ]);

    expect($company)
        ->name->toBe('Acme Corp')
        ->address->toBe('123 Acme St')
        ->phone->toBe('123-456-7890')
        ->email->toBe('info@acme.com')
        ->website->toBe('https://acme.com');

    $this->assertDatabaseHas('companies', [
        'id' => $company->id,
        'name' => 'Acme Corp',
    ]);
});

test('can create employee job position', function () {
    $company = Company::create(['name' => 'Acme Corp']);
    $user = User::factory()->create();
    $role = Role::create(['name' => 'sales_agent', 'guard_name' => 'web']);

    $position = EmployeeJobPosition::create([
        'company_id' => $company->id,
        'user_id' => $user->id,
        'role_id' => $role->id,
    ]);

    expect($position)
        ->company_id->toBe($company->id)
        ->user_id->toBe($user->id)
        ->role_id->toBe($role->id);

    $this->assertDatabaseHas('employee_job_positions', [
        'id' => $position->id,
        'company_id' => $company->id,
        'user_id' => $user->id,
        'role_id' => $role->id,
    ]);

    // Test relationships
    expect($position->company->id)->toBe($company->id)
        ->and($position->user->id)->toBe($user->id)
        ->and($position->role->id)->toBe($role->id);
});
