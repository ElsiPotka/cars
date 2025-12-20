<?php

use App\Models\Brand;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    // Create roles if they don't exist
    if (! Role::where('name', 'admin')->exists()) {
        Role::create(['name' => 'admin']);
    }
    if (! Role::where('name', 'customer')->exists()) {
        Role::create(['name' => 'customer']);
    }
});

test('authenticated user can list brands', function () {
    Brand::factory()->create(['name' => 'Audi', 'country' => 'Germany']);
    Brand::factory()->create(['name' => 'BMW', 'country' => 'Germany']);

    $user = User::factory()->create();

    $response = $this->actingAs($user)->getJson('/api/brands');

    $response->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonFragment(['name' => 'Audi'])
        ->assertJsonFragment(['name' => 'BMW']);
});

test('user can search brands', function () {
    Brand::factory()->create(['name' => 'Audi', 'country' => 'Germany']);
    Brand::factory()->create(['name' => 'Toyota', 'country' => 'Japan']);

    $user = User::factory()->create();

    // Search by name
    $this->actingAs($user)->getJson('/api/brands?search=Audi')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment(['name' => 'Audi']);

    // Search by country
    $this->actingAs($user)->getJson('/api/brands?search=Japan')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment(['name' => 'Toyota']);
});

test('regular user cannot create brand', function () {
    $user = User::factory()->create();
    $user->assignRole('customer');

    $response = $this->actingAs($user)->postJson('/api/brands', [
        'name' => 'New Brand',
        'country' => 'USA',
    ]);

    $response->assertForbidden();
});

test('admin can create brand', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    $response = $this->actingAs($user)->postJson('/api/brands', [
        'name' => 'New Brand',
        'country' => 'USA',
    ]);

    $response->assertCreated()
        ->assertJsonFragment(['name' => 'New Brand']);

    $this->assertDatabaseHas('brands', ['name' => 'New Brand']);
});

test('admin can update brand', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');
    $brand = Brand::factory()->create(['name' => 'Old Name']);

    $response = $this->actingAs($user)->putJson("/api/brands/{$brand->id}", [
        'name' => 'New Name',
    ]);

    $response->assertOk()
        ->assertJsonFragment(['name' => 'New Name']);

    $this->assertDatabaseHas('brands', ['id' => $brand->id, 'name' => 'New Name']);
});

test('admin can delete brand', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');
    $brand = Brand::factory()->create();

    $response = $this->actingAs($user)->deleteJson("/api/brands/{$brand->id}");

    $response->assertOk();
    $this->assertSoftDeleted($brand);
});

test('admin can restore brand', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');
    $brand = Brand::factory()->create();
    $brand->delete();

    $response = $this->actingAs($user)->postJson("/api/brands/{$brand->id}/restore");

    $response->assertOk();
    $this->assertNotSoftDeleted($brand);
});

test('admin can force delete brand', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');
    $brand = Brand::factory()->create();
    $brand->delete();

    $response = $this->actingAs($user)->deleteJson("/api/brands/{$brand->id}/force");

    $response->assertOk();
    $this->assertDatabaseMissing('brands', ['id' => $brand->id]);
});

test('admin can create brand with logo', function () {
    Storage::fake('public');
    $user = User::factory()->create();
    $user->assignRole('admin');

    $file = \Illuminate\Http\UploadedFile::fake()->image('logo.jpg');

    $response = $this->actingAs($user)->postJson('/api/brands', [
        'name' => 'Brand With Logo',
        'country' => 'USA',
        'logo' => $file,
    ]);

    $response->assertCreated();

    $this->assertDatabaseHas('brands', [
        'name' => 'Brand With Logo',
        'logo_path' => 'brands/brand-with-logo.jpg',
    ]);

    Storage::disk('public')->assertExists('brands/brand-with-logo.jpg');
});

test('updating brand with new logo deletes old logo', function () {
    Storage::fake('public');
    $user = User::factory()->create();
    $user->assignRole('admin');

    // Create initial brand with logo
    $file = \Illuminate\Http\UploadedFile::fake()->image('old-logo.jpg');
    $this->actingAs($user)->postJson('/api/brands', [
        'name' => 'Brand Update Test',
        'logo' => $file,
    ]);

    Storage::disk('public')->assertExists('brands/brand-update-test.jpg');
    // Ensure we have the brand ID
    $brand = Brand::where('name', 'Brand Update Test')->first();

    // Update with new logo - use different extension to verify old one is gone
    $newFile = \Illuminate\Http\UploadedFile::fake()->image('new-logo.png');
    $this->actingAs($user)->putJson("/api/brands/{$brand->id}", [
        'name' => 'Brand Update Test',
        'logo' => $newFile,
    ]);

    Storage::disk('public')->assertMissing('brands/brand-update-test.jpg');
    Storage::disk('public')->assertExists('brands/brand-update-test.png');
});

test('updating brand and name deletes old logo file', function () {
    Storage::fake('public');
    $user = User::factory()->create();
    $user->assignRole('admin');

    // Create initial
    $file = \Illuminate\Http\UploadedFile::fake()->image('old.png');
    $this->actingAs($user)->postJson('/api/brands', [
        'name' => 'Old Brand Name',
        'logo' => $file,
    ]);
    Storage::disk('public')->assertExists('brands/old-brand-name.png');
    $brand = Brand::where('name', 'Old Brand Name')->first();

    // Update
    $newFile = \Illuminate\Http\UploadedFile::fake()->image('new.png');
    $this->actingAs($user)->putJson("/api/brands/{$brand->id}", [
        'name' => 'New Brand Name',
        'logo' => $newFile,
    ]);

    Storage::disk('public')->assertMissing('brands/old-brand-name.png');
    Storage::disk('public')->assertExists('brands/new-brand-name.png');
});

test('force deleting brand deletes logo', function () {
    Storage::fake('public');
    $user = User::factory()->create();
    $user->assignRole('admin');

    $file = \Illuminate\Http\UploadedFile::fake()->image('delete-me.jpg');
    $this->actingAs($user)->postJson('/api/brands', [
        'name' => 'Delete Me',
        'logo' => $file,
    ]);

    Storage::disk('public')->assertExists('brands/delete-me.jpg');
    $brand = Brand::where('name', 'Delete Me')->first();
    $brand->delete(); // Soft delete first

    $this->actingAs($user)->deleteJson("/api/brands/{$brand->id}/force");

    Storage::disk('public')->assertMissing('brands/delete-me.jpg');
});
