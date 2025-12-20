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

test('public can view car photo', function () {
    $brand = \App\Models\Brand::factory()->create();
    $carModel = \App\Models\CarModel::factory()->create(['brand_id' => $brand->id]);
    $category = \App\Models\Category::factory()->create();
    $car = Car::create([
        'car_model_id' => $carModel->id,
        'category_id' => $category->id,
        'name' => 'Test Car',
        'year' => 2024,
        'mileage' => 10,
        'transmission' => \App\Enums\CarTransmission::Manual,
        'fuel_type' => \App\Enums\CarFuelType::Petrol,
        'color' => 'Red',
        'price' => 50000,
        'status' => \App\Enums\CarStatus::Available,
    ]);

    $photo = CarPhoto::create(['car_id' => $car->id, 'path' => 'test.jpg']);

    $response = $this->getJson("/api/car-photos/{$photo->id}");

    $response->assertOk()
        ->assertJsonFragment(['id' => $photo->id]);
});

test('admin can upload single photo', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    $brand = \App\Models\Brand::factory()->create();
    $carModel = \App\Models\CarModel::factory()->create(['brand_id' => $brand->id]);
    $category = \App\Models\Category::factory()->create();
    $car = Car::create([
        'car_model_id' => $carModel->id,
        'category_id' => $category->id,
        'name' => 'Test Car',
        'year' => 2024,
        'mileage' => 10,
        'transmission' => \App\Enums\CarTransmission::Manual,
        'fuel_type' => \App\Enums\CarFuelType::Petrol,
        'color' => 'Red',
        'price' => 50000,
        'status' => \App\Enums\CarStatus::Available,
    ]);

    $file = UploadedFile::fake()->image('car.jpg');

    $response = $this->actingAs($user)->postJson('/api/car-photos', [
        'car_id' => $car->id,
        'photo' => $file,
    ]);

    $response->assertCreated();
    $this->assertDatabaseHas('car_photos', ['car_id' => $car->id]);

    $photo = CarPhoto::where('car_id', $car->id)->first();
    Storage::disk('public')->assertExists($photo->path);
});

test('admin can bulk upload photos', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    $brand = \App\Models\Brand::factory()->create();
    $carModel = \App\Models\CarModel::factory()->create(['brand_id' => $brand->id]);
    $category = \App\Models\Category::factory()->create();
    $car = Car::create([
        'car_model_id' => $carModel->id,
        'category_id' => $category->id,
        'name' => 'Test Car',
        'year' => 2024,
        'mileage' => 10,
        'transmission' => \App\Enums\CarTransmission::Manual,
        'fuel_type' => \App\Enums\CarFuelType::Petrol,
        'color' => 'Red',
        'price' => 50000,
        'status' => \App\Enums\CarStatus::Available,
    ]);

    $files = [
        UploadedFile::fake()->image('car1.jpg'),
        UploadedFile::fake()->image('car2.jpg'),
    ];

    $response = $this->actingAs($user)->postJson('/api/car-photos/bulk', [
        'car_id' => $car->id,
        'photos' => $files,
    ]);

    $response->assertCreated();
    $this->assertDatabaseCount('car_photos', 2);
});

test('admin can force delete photo and file', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    // Create car and photo first
    // Simplify for test since we know creating works, or just mock DB entry and file
    $brand = \App\Models\Brand::factory()->create();
    $carModel = \App\Models\CarModel::factory()->create(['brand_id' => $brand->id]);
    $category = \App\Models\Category::factory()->create();
    $car = Car::create([
        'car_model_id' => $carModel->id,
        'category_id' => $category->id,
        'name' => 'Test Car',
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

    $photo->delete(); // Soft delete

    $response = $this->actingAs($user)->deleteJson("/api/car-photos/{$photo->id}/force");

    $response->assertOk();
    $this->assertDatabaseMissing('car_photos', ['id' => $photo->id]);
    Storage::disk('public')->assertMissing($path);
});

test('validation fails for max size', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');
    $brand = \App\Models\Brand::factory()->create();
    $carModel = \App\Models\CarModel::factory()->create(['brand_id' => $brand->id]);
    $category = \App\Models\Category::factory()->create();
    $car = Car::create([
        'car_model_id' => $carModel->id,
        'category_id' => $category->id,
        'name' => 'Test Car',
        'year' => 2024,
        'mileage' => 10,
        'transmission' => \App\Enums\CarTransmission::Manual,
        'fuel_type' => \App\Enums\CarFuelType::Petrol,
        'color' => 'Red',
        'price' => 50000,
        'status' => \App\Enums\CarStatus::Available,
    ]);

    $file = UploadedFile::fake()->create('large.jpg', 10241); // > 10MB

    $response = $this->actingAs($user)->postJson('/api/car-photos', [
        'car_id' => $car->id,
        'photo' => $file,
    ]);

    $response->assertInvalid(['photo']);
});
