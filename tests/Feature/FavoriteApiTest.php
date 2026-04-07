<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->artisan('db:seed', ['--class' => 'VehicleMakeSeeder']);
        $this->artisan('db:seed', ['--class' => 'VehicleModelSeeder']);
    }

    public function test_authenticated_user_can_add_favorite(): void
    {
        $user = User::factory()->create();
        $vehicleOwner = User::factory()->create();
        $vehicle = Vehicle::factory()->create([
            'user_id' => $vehicleOwner->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/favorites', [
                'vehicle_id' => $vehicle->id,
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Vehicle added to favorites',
            ]);

        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'vehicle_id' => $vehicle->id,
        ]);
    }

    public function test_unauthenticated_user_cannot_add_favorite(): void
    {
        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->create([
            'user_id' => $user->id,
            'status' => 'active',
        ]);

        $response = $this->postJson('/api/v1/favorites', [
            'vehicle_id' => $vehicle->id,
        ]);

        $response->assertStatus(401);
    }

    public function test_user_can_list_their_favorites(): void
    {
        $user = User::factory()->create();
        $vehicleOwner = User::factory()->create();
        
        $vehicle1 = Vehicle::factory()->create([
            'user_id' => $vehicleOwner->id,
            'status' => 'active',
        ]);
        
        $vehicle2 = Vehicle::factory()->create([
            'user_id' => $vehicleOwner->id,
            'status' => 'active',
        ]);

        // Use favoriteVehicles() which is BelongsToMany (supports attach)
        $user->favoriteVehicles()->attach([$vehicle1->id, $vehicle2->id]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/favorites');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_user_can_remove_favorite(): void
    {
        $user = User::factory()->create();
        $vehicleOwner = User::factory()->create();
        $vehicle = Vehicle::factory()->create([
            'user_id' => $vehicleOwner->id,
            'status' => 'active',
        ]);

        $user->favoriteVehicles()->attach($vehicle->id);

        $response = $this->actingAs($user, 'sanctum')
            ->deleteJson("/api/v1/favorites/{$vehicle->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Vehicle removed from favorites',
            ]);

        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'vehicle_id' => $vehicle->id,
        ]);
    }

    public function test_cannot_add_duplicate_favorite(): void
    {
        $user = User::factory()->create();
        $vehicleOwner = User::factory()->create();
        $vehicle = Vehicle::factory()->create([
            'user_id' => $vehicleOwner->id,
            'status' => 'active',
        ]);

        $user->favoriteVehicles()->attach($vehicle->id);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/favorites', [
                'vehicle_id' => $vehicle->id,
            ]);

        $response->assertStatus(422);
    }
}
