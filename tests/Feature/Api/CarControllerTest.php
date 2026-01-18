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

test('admin can create car for their company', function () {
    $company = Company::create(['name' => 'My Company']);
    $user = User::factory()->create();
    $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    $user->assignRole($role);
    EmployeeJobPosition::create([
        'company_id' => $company->id,
        'user_id' => $user->id,
        'role_id' => $role->id,
    ]);

    Sanctum::actingAs($user);

    $carModel = CarModel::factory()->create();
    $category = Category::create(['name' => 'SUV', 'description' => 'Sport Utility Vehicle']);

    $response = $this->postJson('/api/cars', [
        'car_model_id' => $carModel->id,
        'category_id' => $category->id,
        'name' => 'New Car',
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

    $response->assertCreated();
    $this->assertDatabaseHas('cars', [
        'name' => 'New Car',
        'company_id' => $company->id,
    ]);
});

test('user without company cannot create car', function () {
    $user = User::factory()->create();
    $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    $user->assignRole($role);
    
    Sanctum::actingAs($user);

    $carModel = CarModel::factory()->create();
    $category = Category::create(['name' => 'SUV', 'description' => 'Sport Utility Vehicle']);

    $response = $this->postJson('/api/cars', [
        'car_model_id' => $carModel->id,
        'category_id' => $category->id,
        'name' => 'New Car',
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

    $response->assertForbidden();
    $response->assertJson(['message' => 'User does not belong to a company.']);
});

test('admin cannot update another companies car', function () {
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
    $category = Category::create(['name' => 'SUV', 'description' => 'Sport Utility Vehicle']);
    
    $car2 = Car::create([
        'name' => 'Company 2 Car',
        'company_id' => $company2->id,
        'car_model_id' => CarModel::factory()->create()->id,
        'category_id' => $category->id,
        'year' => 2022,
        'price' => 30000,
        'mileage' => 5000,
        'status' => CarStatus::Available->value,
        'transmission' => CarTransmission::Manual->value,
        'fuel_type' => CarFuelType::Diesel->value,
        'engine_size' => '2.0L',
        'color' => 'Black',
        'description' => 'A nice car',
    ]);

    Sanctum::actingAs($user1);

    $response = $this->putJson("/api/cars/{$car2->id}", [
        'name' => 'Hacked Name',
        'status' => CarStatus::Available->value,
        'transmission' => CarTransmission::Manual->value,
        'fuel_type' => CarFuelType::Diesel->value,
        'engine_size' => '2.0L',
        'color' => 'Black',
        'description' => 'A nice car',
    ]);

    $response->assertForbidden();
    $response->assertJson(['message' => 'Unauthorized access to this car.']);
});

test('admin can update their companies car', function () {
    $company = Company::create(['name' => 'My Company']);
    $user = User::factory()->create();
    $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    $user->assignRole($role);
    EmployeeJobPosition::create([
        'company_id' => $company->id,
        'user_id' => $user->id,
        'role_id' => $role->id,
    ]);

    $category = Category::create(['name' => 'SUV', 'description' => 'Sport Utility Vehicle']);
    $car = Car::create([
        'name' => 'My Car',
        'company_id' => $company->id,
        'car_model_id' => CarModel::factory()->create()->id,
        'category_id' => $category->id,
        'year' => 2022,
        'price' => 30000,
        'mileage' => 5000,
        'status' => CarStatus::Available->value,
        'transmission' => CarTransmission::Manual->value,
        'fuel_type' => CarFuelType::Diesel->value,
        'engine_size' => '2.0L',
        'color' => 'Black',
        'description' => 'A nice car',
    ]);

    Sanctum::actingAs($user);

    $response = $this->putJson("/api/cars/{$car->id}", [
        'name' => 'Updated Name',
        'year' => 2022,
        'price' => 30000,
        'mileage' => 5000,
        'status' => CarStatus::Available->value,
        'transmission' => CarTransmission::Manual->value,
        'fuel_type' => CarFuelType::Diesel->value,
        'engine_size' => '2.0L',
        'color' => 'Black',
        'description' => 'Updated description',
    ]);

    $response->assertOk();
    $this->assertDatabaseHas('cars', [
        'id' => $car->id,
        'name' => 'Updated Name',
    ]);
});

test('admin cannot delete another companies car', function () {
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
    $category = Category::create(['name' => 'SUV', 'description' => 'Sport Utility Vehicle']);
    
    $car2 = Car::create([
        'name' => 'Company 2 Car',
        'company_id' => $company2->id,
        'car_model_id' => CarModel::factory()->create()->id,
        'category_id' => $category->id,
        'year' => 2022,
        'price' => 30000,
        'mileage' => 5000,
        'status' => CarStatus::Available->value,
        'transmission' => CarTransmission::Manual->value,
        'fuel_type' => CarFuelType::Diesel->value,
        'engine_size' => '2.0L',
        'color' => 'Black',
        'description' => 'A nice car',
    ]);

    Sanctum::actingAs($user1);

    $response = $this->deleteJson("/api/cars/{$car2->id}");

    $response->assertForbidden();
    $this->assertDatabaseHas('cars', ['id' => $car2->id]);
});
