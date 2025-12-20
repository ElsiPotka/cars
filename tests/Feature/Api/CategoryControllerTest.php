<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\User;
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

test('authenticated user can list categories', function () {
    Category::factory()->create(['name' => 'SUV']);
    Category::factory()->create(['name' => 'Sedan']);

    $user = User::factory()->create();

    $response = $this->actingAs($user)->getJson('/api/categories');

    $response->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonFragment(['name' => 'SUV'])
        ->assertJsonFragment(['name' => 'Sedan']);
});

test('user can search categories', function () {
    Category::factory()->create(['name' => 'SUV']);
    Category::factory()->create(['name' => 'Sedan']);

    $user = User::factory()->create();

    $this->actingAs($user)->getJson('/api/categories?search=SUV')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment(['name' => 'SUV']);
});

test('regular user cannot create category', function () {
    $user = User::factory()->create();
    $user->assignRole('customer');

    $response = $this->actingAs($user)->postJson('/api/categories', [
        'name' => 'New Category',
    ]);

    $response->assertForbidden();
});

test('admin can create category', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    $response = $this->actingAs($user)->postJson('/api/categories', [
        'name' => 'New Category',
        'description' => 'Test Description',
    ]);

    $response->assertCreated()
        ->assertJsonFragment(['name' => 'New Category']);

    $this->assertDatabaseHas('categories', ['name' => 'New Category']);
});

test('admin can update category', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');
    $category = Category::factory()->create(['name' => 'Old Name']);

    $response = $this->actingAs($user)->putJson("/api/categories/{$category->id}", [
        'name' => 'New Name',
    ]);

    $response->assertOk()
        ->assertJsonFragment(['name' => 'New Name']);

    $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => 'New Name']);
});

test('admin can delete category', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');
    $category = Category::factory()->create();

    $response = $this->actingAs($user)->deleteJson("/api/categories/{$category->id}");

    $response->assertOk();
    $this->assertSoftDeleted($category);
});

test('admin can restore category', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');
    $category = Category::factory()->create();
    $category->delete();

    $response = $this->actingAs($user)->postJson("/api/categories/{$category->id}/restore");

    $response->assertOk();
    $this->assertNotSoftDeleted($category);
});

test('admin can force delete category', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');
    $category = Category::factory()->create();
    $category->delete();

    $response = $this->actingAs($user)->deleteJson("/api/categories/{$category->id}/force");

    $response->assertOk();
    $this->assertDatabaseMissing('categories', ['id' => $category->id]);
});
