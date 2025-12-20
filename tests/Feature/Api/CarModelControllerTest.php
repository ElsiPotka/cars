<?php

namespace Tests\Feature\Api;

use App\Models\Brand;
use App\Models\CarModel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CarModelControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_car_models()
    {
        $user = User::factory()->create();
        $brand = Brand::factory()->create();
        CarModel::factory()->count(3)->create(['brand_id' => $brand->id]);

        $response = $this->actingAs($user)->getJson('/api/car-models');

        $response->assertSuccessful()
            ->assertJsonCount(3, 'data');
    }

    public function test_store_creates_car_model()
    {
        $user = User::factory()->create();
        $this->seed(\Database\Seeders\RoleAndPermissionSeeder::class);
        $user->assignRole('admin');

        $brand = Brand::factory()->create();

        $data = [
            'name' => 'Corolla',
            'brand_id' => $brand->id,
            'year' => 2023,
        ];

        $response = $this->actingAs($user)->postJson('/api/car-models', $data);

        $response->assertCreated()
            ->assertJsonFragment(['name' => 'Corolla']);

        $this->assertDatabaseHas('car_models', ['name' => 'Corolla']);
    }

    public function test_store_validates_brand_id()
    {
        $user = User::factory()->create();
        $this->seed(\Database\Seeders\RoleAndPermissionSeeder::class);
        $user->assignRole('admin');

        $data = [
            'name' => 'Corolla',
            'brand_id' => 'invalid-uuid',
            'year' => 2023,
        ];

        $response = $this->actingAs($user)->postJson('/api/car-models', $data);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['brand_id']);
    }

    public function test_store_validates_year()
    {
        $user = User::factory()->create();
        $this->seed(\Database\Seeders\RoleAndPermissionSeeder::class);
        $user->assignRole('admin');

        $brand = Brand::factory()->create();

        $data = [
            'name' => 'Corolla',
            'brand_id' => $brand->id,
            'year' => 1800,
        ];

        $response = $this->actingAs($user)->postJson('/api/car-models', $data);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['year']);
    }

    public function test_update_updates_car_model()
    {
        $user = User::factory()->create();
        $this->seed(\Database\Seeders\RoleAndPermissionSeeder::class);
        $user->assignRole('admin');

        $brand = Brand::factory()->create();
        $model = CarModel::factory()->create(['name' => 'Old Name', 'brand_id' => $brand->id]);

        $data = [
            'name' => 'New Name',
        ];

        $response = $this->actingAs($user)->putJson("/api/car-models/{$model->id}", $data);

        $response->assertSuccessful()
            ->assertJsonFragment(['name' => 'New Name']);

        $this->assertDatabaseHas('car_models', ['name' => 'New Name']);
    }

    public function test_destroy_deletes_car_model()
    {
        $user = User::factory()->create();
        $this->seed(\Database\Seeders\RoleAndPermissionSeeder::class);
        $user->assignRole('admin');

        $brand = Brand::factory()->create();
        $model = CarModel::factory()->create(['brand_id' => $brand->id]);

        $response = $this->actingAs($user)->deleteJson("/api/car-models/{$model->id}");

        $response->assertSuccessful();

        $this->assertSoftDeleted('car_models', ['id' => $model->id]);
    }

    public function test_non_admin_cannot_manage_car_models()
    {
        $user = User::factory()->create();
        $brand = Brand::factory()->create();
        $model = CarModel::factory()->create(['brand_id' => $brand->id]);

        // Try to create
        $this->actingAs($user)->postJson('/api/car-models', ['name' => 'New', 'brand_id' => $brand->id, 'year' => 2023])
            ->assertForbidden();

        // Try to update
        $this->actingAs($user)->putJson("/api/car-models/{$model->id}", ['name' => 'Updated'])
            ->assertForbidden();

        // Try to delete
        $this->actingAs($user)->deleteJson("/api/car-models/{$model->id}")
            ->assertForbidden();

        // Try to restore
        $this->actingAs($user)->postJson("/api/car-models/{$model->id}/restore")
            ->assertForbidden();

        // Try to force delete
        $this->actingAs($user)->deleteJson("/api/car-models/{$model->id}/force")
            ->assertForbidden();
    }

    public function test_show_includes_brand_relationship()
    {
        $user = User::factory()->create();
        $brand = Brand::factory()->create(['name' => 'Toyota']);
        $model = CarModel::factory()->create(['brand_id' => $brand->id, 'name' => 'Camry']);

        $response = $this->actingAs($user)->getJson("/api/car-models/{$model->id}");

        $response->assertSuccessful()
            ->assertJsonPath('data.brand.name', 'Toyota');
    }
}
