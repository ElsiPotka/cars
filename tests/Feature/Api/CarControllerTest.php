<?php

use App\Models\Car;
use App\Models\CarPhoto;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    if (! Role::where('name', 'admin')->exists()) {
        Role::create(['name' => 'admin']);
    }
    if (! Role::where('name', 'customer')->exists()) {
        Role::create(['name' => 'customer']);
    }
    Storage::fake('public');
});

test('admin can create car with photos', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    $brand = \App\Models\Brand::factory()->create();
    $carModel = \App\Models\CarModel::factory()->create(['brand_id' => $brand->id]);
    $category = \App\Models\Category::factory()->create();

    $files = [
        UploadedFile::fake()->image('car1.jpg'),
        UploadedFile::fake()->image('car2.jpg'),
    ];

    $response = $this->actingAs($user)->postJson('/api/cars', [
        'car_model_id' => $carModel->id,
        'category_id' => $category->id,
        'name' => 'Test Car',
        'year' => 2024,
        'mileage' => 10,
        'transmission' => \App\Enums\CarTransmission::Manual->value,
        'fuel_type' => \App\Enums\CarFuelType::Petrol->value,
        'engine_size' => '2.0L',
        'color' => 'Red',
        'price' => 50000,
        'status' => \App\Enums\CarStatus::Available->value,
        'photos' => $files,
    ]);

    $response->assertCreated();
    $this->assertDatabaseHas('cars', ['name' => 'Test Car']);
    $this->assertDatabaseCount('car_photos', 2);

    $car = Car::where('name', 'Test Car')->first();
    $photos = $car->photos;
    foreach ($photos as $photo) {
        Storage::disk('public')->assertExists($photo->path);
    }
});

test('admin can update car and manage photos', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    $brand = \App\Models\Brand::factory()->create();
    $carModel = \App\Models\CarModel::factory()->create(['brand_id' => $brand->id]);
    $category = \App\Models\Category::factory()->create();

    $car = Car::create([
        'car_model_id' => $carModel->id,
        'category_id' => $category->id,
        'name' => 'Original Car',
        'year' => 2024,
        'mileage' => 10,
        'transmission' => \App\Enums\CarTransmission::Manual,
        'fuel_type' => \App\Enums\CarFuelType::Petrol,
        'color' => 'Red',
        'price' => 50000,
        'status' => \App\Enums\CarStatus::Available,
    ]);

    $path = "cars/{$car->id}/test.jpg";
    Storage::disk('public')->put($path, 'content');
    $photo = CarPhoto::create(['car_id' => $car->id, 'path' => $path]);

    $newFiles = [
        UploadedFile::fake()->image('new.jpg'),
    ];

    $response = $this->actingAs($user)->putJson("/api/cars/{$car->id}", [
        'name' => 'Updated Car',
        'deleted_photos' => [$photo->id],
        'photos' => $newFiles,
    ]);

    $response->assertOk();
    $this->assertDatabaseHas('cars', ['name' => 'Updated Car']);

    // Check old photo deleted
    $this->assertSoftDeleted('car_photos', ['id' => $photo->id]);
    Storage::disk('public')->assertMissing($path);

    // Check new photo added
    expect(CarPhoto::count())->toBe(1);
    $newPhoto = CarPhoto::where('car_id', $car->id)->latest()->first();
    Storage::disk('public')->assertExists($newPhoto->path);
});

test('validation fails for invalid enum', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    $brand = \App\Models\Brand::factory()->create();
    $carModel = \App\Models\CarModel::factory()->create(['brand_id' => $brand->id]);
    $category = \App\Models\Category::factory()->create();

    $response = $this->actingAs($user)->postJson('/api/cars', [
        'car_model_id' => $carModel->id,
        'category_id' => $category->id,
        'name' => 'Test Car',
        'year' => 2024,
        'mileage' => 10,
        'transmission' => 'InvalidTransmission', // Invalid
        'fuel_type' => \App\Enums\CarFuelType::Petrol->value,
        'color' => 'Red',
        'price' => 50000,
        'status' => \App\Enums\CarStatus::Available->value,
    ]);

    $response->assertInvalid(['transmission']);
});

test('public can list cars', function () {
    $brand = \App\Models\Brand::factory()->create();
    $carModel = \App\Models\CarModel::factory()->create(['brand_id' => $brand->id]);
    $category = \App\Models\Category::factory()->create();

    Car::create([
        'car_model_id' => $carModel->id,
        'category_id' => $category->id,
        'name' => 'Car One',
        'year' => 2024,
        'mileage' => 10,
        'transmission' => \App\Enums\CarTransmission::Manual,
        'fuel_type' => \App\Enums\CarFuelType::Petrol,
        'color' => 'Red',
        'price' => 50000,
        'status' => \App\Enums\CarStatus::Available,
    ]);

    Car::create([
        'car_model_id' => $carModel->id,
        'category_id' => $category->id,
        'name' => 'Car Two',
        'year' => 2024,
        'mileage' => 10,
        'transmission' => \App\Enums\CarTransmission::Manual,
        'fuel_type' => \App\Enums\CarFuelType::Petrol,
        'color' => 'Blue',
        'price' => 60000,
        'status' => \App\Enums\CarStatus::Available,
    ]);

    $response = $this->getJson('/api/cars');

    $response->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonFragment(['name' => 'Car One'])
        ->assertJsonFragment(['name' => 'Car Two']);
});

test('public can search cars', function () {
    $brand = \App\Models\Brand::factory()->create(['name' => 'Toyota']);
    $carModel = \App\Models\CarModel::factory()->create(['brand_id' => $brand->id, 'name' => 'Corolla']);
    $category = \App\Models\Category::factory()->create();

    Car::create([
        'car_model_id' => $carModel->id,
        'category_id' => $category->id,
        'name' => 'Special Edition',
        'year' => 2024,
        'mileage' => 10,
        'transmission' => \App\Enums\CarTransmission::Manual,
        'fuel_type' => \App\Enums\CarFuelType::Petrol,
        'color' => 'Red',
        'price' => 50000,
        'status' => \App\Enums\CarStatus::Available,
        'description' => 'A very reliable car',
    ]);

    // Search by name
    $this->getJson('/api/cars?search=Special')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment(['name' => 'Special Edition']);

    // Search by description
    $this->getJson('/api/cars?search=reliable')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment(['name' => 'Special Edition']);

    // Search by brand
    $this->getJson('/api/cars?search=Toyota')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment(['name' => 'Special Edition']);

    // Search by model
    $this->getJson('/api/cars?search=Corolla')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment(['name' => 'Special Edition']);

    // No match
    $this->getJson('/api/cars?search=Missing')
        ->assertOk()
        ->assertJsonCount(0, 'data');
});

test('public can view single car', function () {
    $brand = \App\Models\Brand::factory()->create();
    $carModel = \App\Models\CarModel::factory()->create(['brand_id' => $brand->id]);
    $category = \App\Models\Category::factory()->create();

    $car = Car::create([
        'car_model_id' => $carModel->id,
        'category_id' => $category->id,
        'name' => 'Car One',
        'year' => 2024,
        'mileage' => 10,
        'transmission' => \App\Enums\CarTransmission::Manual,
        'fuel_type' => \App\Enums\CarFuelType::Petrol,
        'color' => 'Red',
        'price' => 50000,
        'status' => \App\Enums\CarStatus::Available,
    ]);

    $response = $this->getJson("/api/cars/{$car->id}");

    $response->assertOk()
        ->assertJsonFragment(['name' => 'Car One']);
});

test('regular user cannot create car', function () {
    $user = User::factory()->create();
    $user->assignRole('customer');

    $response = $this->actingAs($user)->postJson('/api/cars', []);

    $response->assertForbidden();
});

test('regular user cannot update car', function () {
    $user = User::factory()->create();
    $user->assignRole('customer');

    $brand = \App\Models\Brand::factory()->create();
    $carModel = \App\Models\CarModel::factory()->create(['brand_id' => $brand->id]);
    $category = \App\Models\Category::factory()->create();
    $car = Car::create([
        'car_model_id' => $carModel->id,
        'category_id' => $category->id,
        'name' => 'Car One',
        'year' => 2024,
        'mileage' => 10,
        'transmission' => \App\Enums\CarTransmission::Manual,
        'fuel_type' => \App\Enums\CarFuelType::Petrol,
        'color' => 'Red',
        'price' => 50000,
        'status' => \App\Enums\CarStatus::Available,
    ]);

    $response = $this->actingAs($user)->putJson("/api/cars/{$car->id}", []);

    $response->assertForbidden();
});

test('regular user cannot delete car', function () {
    $user = User::factory()->create();
    $user->assignRole('customer');
    $brand = \App\Models\Brand::factory()->create();
    $carModel = \App\Models\CarModel::factory()->create(['brand_id' => $brand->id]);
    $category = \App\Models\Category::factory()->create();
    $car = Car::create([
        'car_model_id' => $carModel->id,
        'category_id' => $category->id,
        'name' => 'Car One',
        'year' => 2024,
        'mileage' => 10,
        'transmission' => \App\Enums\CarTransmission::Manual,
        'fuel_type' => \App\Enums\CarFuelType::Petrol,
        'color' => 'Red',
        'price' => 50000,
        'status' => \App\Enums\CarStatus::Available,
    ]);

    $response = $this->actingAs($user)->deleteJson("/api/cars/{$car->id}");

    $response->assertForbidden();
});

test('regular user cannot restore car', function () {
    $user = User::factory()->create();
    $user->assignRole('customer');
    $brand = \App\Models\Brand::factory()->create();
    $carModel = \App\Models\CarModel::factory()->create(['brand_id' => $brand->id]);
    $category = \App\Models\Category::factory()->create();
    $car = Car::create([
        'car_model_id' => $carModel->id,
        'category_id' => $category->id,
        'name' => 'Car One',
        'year' => 2024,
        'mileage' => 10,
        'transmission' => \App\Enums\CarTransmission::Manual,
        'fuel_type' => \App\Enums\CarFuelType::Petrol,
        'color' => 'Red',
        'price' => 50000,
        'status' => \App\Enums\CarStatus::Available,
    ]);
    $car->delete();

    $response = $this->actingAs($user)->postJson("/api/cars/{$car->id}/restore");

    $response->assertForbidden();
});

test('regular user cannot force delete car', function () {
    $user = User::factory()->create();
    $user->assignRole('customer');
    $brand = \App\Models\Brand::factory()->create();
    $carModel = \App\Models\CarModel::factory()->create(['brand_id' => $brand->id]);
    $category = \App\Models\Category::factory()->create();
    $car = Car::create([
        'car_model_id' => $carModel->id,
        'category_id' => $category->id,
        'name' => 'Car One',
        'year' => 2024,
        'mileage' => 10,
        'transmission' => \App\Enums\CarTransmission::Manual,
        'fuel_type' => \App\Enums\CarFuelType::Petrol,
        'color' => 'Red',
        'price' => 50000,
        'status' => \App\Enums\CarStatus::Available,
    ]);
    $car->delete();

    $response = $this->actingAs($user)->deleteJson("/api/cars/{$car->id}/force");

    $response->assertForbidden();
});

test('admin can delete car', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    $brand = \App\Models\Brand::factory()->create();
    $carModel = \App\Models\CarModel::factory()->create(['brand_id' => $brand->id]);
    $category = \App\Models\Category::factory()->create();
    $car = Car::create([
        'car_model_id' => $carModel->id,
        'category_id' => $category->id,
        'name' => 'Delete Me',
        'year' => 2024,
        'mileage' => 10,
        'transmission' => \App\Enums\CarTransmission::Manual,
        'fuel_type' => \App\Enums\CarFuelType::Petrol,
        'color' => 'Red',
        'price' => 50000,
        'status' => \App\Enums\CarStatus::Available,
    ]);

    $response = $this->actingAs($user)->deleteJson("/api/cars/{$car->id}");

    $response->assertOk();
    $this->assertSoftDeleted($car);
});

test('admin can restore car', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    $brand = \App\Models\Brand::factory()->create();
    $carModel = \App\Models\CarModel::factory()->create(['brand_id' => $brand->id]);
    $category = \App\Models\Category::factory()->create();
    $car = Car::create([
        'car_model_id' => $carModel->id,
        'category_id' => $category->id,
        'name' => 'Restore Me',
        'year' => 2024,
        'mileage' => 10,
        'transmission' => \App\Enums\CarTransmission::Manual,
        'fuel_type' => \App\Enums\CarFuelType::Petrol,
        'color' => 'Red',
        'price' => 50000,
        'status' => \App\Enums\CarStatus::Available,
    ]);
    $car->delete();

    $response = $this->actingAs($user)->postJson("/api/cars/{$car->id}/restore");

    $response->assertOk();
    $this->assertNotSoftDeleted($car);
});

test('admin can force delete car', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    $brand = \App\Models\Brand::factory()->create();
    $carModel = \App\Models\CarModel::factory()->create(['brand_id' => $brand->id]);
    $category = \App\Models\Category::factory()->create();
    $car = Car::create([
        'car_model_id' => $carModel->id,
        'category_id' => $category->id,
        'name' => 'Force Delete Me',
        'year' => 2024,
        'mileage' => 10,
        'transmission' => \App\Enums\CarTransmission::Manual,
        'fuel_type' => \App\Enums\CarFuelType::Petrol,
        'color' => 'Red',
        'price' => 50000,
        'status' => \App\Enums\CarStatus::Available,
    ]);
    $car->delete();

    $response = $this->actingAs($user)->deleteJson("/api/cars/{$car->id}/force");

    $response->assertOk();
    $this->assertDatabaseMissing('cars', ['id' => $car->id]);
});
