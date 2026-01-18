<?php

use App\Enums\CarFuelType;
use App\Enums\CarStatus;
use App\Enums\CarTransmission;
use App\Models\Car;
use App\Models\CarModel;
use App\Models\Category;
use App\Models\Company;
use App\Models\EmployeeJobPosition;
use App\Models\Role;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
});

test('admin can view their companies cars', function () {
    $company = Company::create(['name' => 'My Company']);
    $user = User::factory()->create();
    $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    $user->assignRole($role);
    EmployeeJobPosition::create([
        'company_id' => $company->id,
        'user_id' => $user->id,
        'role_id' => $role->id,
    ]);

    $car1 = Car::create([
        'name' => 'My Company Car 1',
        'company_id' => $company->id,
        'car_model_id' => CarModel::factory()->create()->id,
        'category_id' => Category::create(['name' => 'SUV', 'description' => 'Sport Utility Vehicle'])->id,
        'year' => 2023,
        'price' => 50000,
        'mileage' => 1000,
        'status' => CarStatus::Available->value,
        'transmission' => CarTransmission::Automatic->value,
        'fuel_type' => CarFuelType::Petrol->value,
        'engine_size' => '2.0L',
        'color' => 'Red',
        'description' => 'A nice car',
    ]);

    $car2 = Car::create([
         'name' => 'My Company Car 2',
         'company_id' => $company->id,
         'car_model_id' => CarModel::factory()->create()->id,
         'category_id' => Category::create(['name' => 'Sedan', 'description' => 'Sedan Vehicle'])->id,
         'year' => 2022,
         'price' => 40000,
         'mileage' => 15000,
         'status' => CarStatus::Available->value,
         'transmission' => CarTransmission::Automatic->value,
         'fuel_type' => CarFuelType::Petrol->value,
         'engine_size' => '1.5L',
         'color' => 'Blue',
         'description' => 'Another nice car',
     ]);

    Sanctum::actingAs($user);

    $response = $this->getJson("/api/companies/{$company->id}/cars");

    $response->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonFragment(['name' => 'My Company Car 1'])
        ->assertJsonFragment(['name' => 'My Company Car 2']);
});

test('admin cannot view another companies cars', function () {
    $company1 = Company::create(['name' => 'Company 1']);
    $user1 = User::factory()->create();
    $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    $user1->assignRole($role);
    EmployeeJobPosition::create([
        'company_id' => $company1->id,
        'user_id' => $user1->id,
        'role_id' => $role->id,
    ]);

    $company2 = Company::create(['name' => 'Company 2']);
    Car::create([
        'name' => 'Company 2 Car',
        'company_id' => $company2->id,
        'car_model_id' => CarModel::factory()->create()->id,
        'category_id' => Category::create(['name' => 'SUV', 'description' => 'Sport Utility Vehicle'])->id,
        'year' => 2023,
        'price' => 50000,
        'mileage' => 1000,
        'status' => CarStatus::Available->value,
        'transmission' => CarTransmission::Automatic->value,
        'fuel_type' => CarFuelType::Petrol->value,
        'engine_size' => '2.0L',
        'color' => 'Red',
        'description' => 'A nice car',
    ]);

    Sanctum::actingAs($user1);

    $response = $this->getJson("/api/companies/{$company2->id}/cars");

    $response->assertForbidden();
});
