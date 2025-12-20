<?php

namespace Tests\Feature\Api;

use App\Models\CarFeature;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CarFeatureControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Assume we have an admin role
        // Attaching role to user would depend on the implementation of roles/permissions
        // For now, let's assume standard 'role:admin' middleware checks a role on the user
    }

    public function test_index_returns_features()
    {
        $user = User::factory()->create();
        CarFeature::factory()->count(3)->create();

        $response = $this->actingAs($user)->getJson('/api/car-features');

        $response->assertSuccessful()
            ->assertJsonCount(3, 'data');
    }

    public function test_store_creates_feature()
    {
        $user = User::factory()->create();
        // Adding 'admin' role if needed. Assuming the middleware checks for 'admin' role name.
        // We might need to seed roles or just mock the check if it's complicated.
        // Let's assume Spatie permission or similar
        $this->seed(\Database\Seeders\RoleAndPermissionSeeder::class);
        $user->assignRole('admin');

        $data = [
            'name' => 'New Feature',
            'description' => 'A description',
        ];

        $response = $this->actingAs($user)->postJson('/api/car-features', $data);

        $response->assertCreated()
            ->assertJsonFragment(['name' => 'New Feature']);

        $this->assertDatabaseHas('car_features', ['name' => 'New Feature']);
    }

    public function test_store_validates_unique_name()
    {
        $user = User::factory()->create();
        $this->seed(\Database\Seeders\RoleAndPermissionSeeder::class);
        $user->assignRole('admin');

        CarFeature::create(['name' => 'Existing Feature']);

        $data = [
            'name' => 'Existing Feature',
        ];

        $response = $this->actingAs($user)->postJson('/api/car-features', $data);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    public function test_update_updates_feature()
    {
        $user = User::factory()->create();
        $this->seed(\Database\Seeders\RoleAndPermissionSeeder::class);
        $user->assignRole('admin');

        $feature = CarFeature::create(['name' => 'Old Name']);

        $data = [
            'name' => 'New Name',
        ];

        $response = $this->actingAs($user)->putJson("/api/car-features/{$feature->id}", $data);

        $response->assertSuccessful()
            ->assertJsonFragment(['name' => 'New Name']);

        $this->assertDatabaseHas('car_features', ['name' => 'New Name']);
    }

    public function test_destroy_deletes_feature()
    {
        $user = User::factory()->create();
        $this->seed(\Database\Seeders\RoleAndPermissionSeeder::class);
        $user->assignRole('admin');

        $feature = CarFeature::create(['name' => 'To Delete']);

        $response = $this->actingAs($user)->deleteJson("/api/car-features/{$feature->id}");

        $response->assertSuccessful();

        $this->assertSoftDeleted('car_features', ['id' => $feature->id]);
    }

    public function test_non_admin_cannot_manage_features()
    {
        $user = User::factory()->create();
        // User does not have admin role

        $feature = CarFeature::factory()->create();

        // Try to create
        $this->actingAs($user)->postJson('/api/car-features', ['name' => 'New'])
            ->assertForbidden();

        // Try to update
        $this->actingAs($user)->putJson("/api/car-features/{$feature->id}", ['name' => 'Updated'])
            ->assertForbidden();

        // Try to delete
        $this->actingAs($user)->deleteJson("/api/car-features/{$feature->id}")
            ->assertForbidden();

        // Try to restore
        $this->actingAs($user)->postJson("/api/car-features/{$feature->id}/restore")
            ->assertForbidden();

        // Try to force delete
        $this->actingAs($user)->deleteJson("/api/car-features/{$feature->id}/force")
            ->assertForbidden();
    }
}
