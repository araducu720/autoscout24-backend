<?php

namespace Tests\Feature;

use App\Models\Vehicle;
use App\Models\VehicleMake;
use App\Models\VehicleModel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VehicleApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed basic data
        $this->artisan('db:seed', ['--class' => 'VehicleMakeSeeder']);
        $this->artisan('db:seed', ['--class' => 'VehicleModelSeeder']);
    }

    public function test_can_list_vehicles(): void
    {
        // Create test vehicles
        $user = User::factory()->create();
        Vehicle::factory()->count(15)->create([
            'user_id' => $user->id,
            'status' => 'active',
        ]);

        // Request with per_page=15 to get all vehicles on one page
        $response = $this->getJson('/api/v1/vehicles?per_page=15');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'price',
                        'year',
                        'mileage',
                        'make',
                        'model',
                        'status',
                    ]
                ],
                'meta' => [
                    'current_page',
                    'total',
                    'per_page',
                ],
            ])
            ->assertJsonCount(15, 'data');
    }

    public function test_can_get_single_vehicle(): void
    {
        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->create([
            'user_id' => $user->id,
            'status' => 'active',
        ]);

        $response = $this->getJson("/api/v1/vehicles/{$vehicle->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $vehicle->id,
                    'title' => $vehicle->title,
                    'price' => $vehicle->price,
                ]
            ]);
    }

    public function test_can_filter_vehicles_by_price(): void
    {
        $user = User::factory()->create();
        
        Vehicle::factory()->create([
            'user_id' => $user->id,
            'status' => 'active',
            'price' => '10000.00',
        ]);
        
        Vehicle::factory()->create([
            'user_id' => $user->id,
            'status' => 'active',
            'price' => '30000.00',
        ]);

        $response = $this->getJson('/api/v1/vehicles?price_min=20000&price_max=40000');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_can_search_vehicles(): void
    {
        $user = User::factory()->create();
        
        Vehicle::factory()->create([
            'user_id' => $user->id,
            'status' => 'active',
            'title' => 'BMW 320d Sport Package',
        ]);
        
        Vehicle::factory()->create([
            'user_id' => $user->id,
            'status' => 'active',
            'title' => 'Mercedes C-Class',
        ]);

        $response = $this->getJson('/api/v1/vehicles?search=BMW');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_increments_view_count_on_show(): void
    {
        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->create([
            'user_id' => $user->id,
            'status' => 'active',
            'views_count' => 0,
        ]);

        $this->getJson("/api/v1/vehicles/{$vehicle->id}");

        $this->assertDatabaseHas('vehicles', [
            'id' => $vehicle->id,
            'views_count' => 1,
        ]);
    }

    public function test_only_shows_active_vehicles(): void
    {
        $user = User::factory()->create();
        
        Vehicle::factory()->create([
            'user_id' => $user->id,
            'status' => 'active',
        ]);
        
        Vehicle::factory()->create([
            'user_id' => $user->id,
            'status' => 'sold',
        ]);

        $response = $this->getJson('/api/v1/vehicles');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }
}
