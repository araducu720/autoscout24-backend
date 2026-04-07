<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vehicle;
use App\Models\TestDriveRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Test Drive Request API Tests
 * Tests booking and listing test drive appointments
 */
class TestDriveApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Vehicle $vehicle;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\VehicleMakeSeeder::class);
        $this->seed(\Database\Seeders\VehicleModelSeeder::class);

        $this->user = User::factory()->create();
        $seller = User::factory()->create();
        $this->vehicle = Vehicle::factory()->create([
            'user_id' => $seller->id,
            'status' => 'active',
        ]);
    }

    public function test_can_request_test_drive(): void
    {
        $response = $this->postJson('/api/v1/test-drives', [
            'vehicle_id' => $this->vehicle->id,
            'name' => 'Hans Schmidt',
            'email' => 'hans@example.de',
            'phone' => '+49 172 1234567',
            'preferred_date' => now()->addDays(3)->format('Y-m-d'),
            'preferred_time' => '14:00',
            'message' => 'I would like to test drive this car.',
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('test_drive_requests', [
            'vehicle_id' => $this->vehicle->id,
            'name' => 'Hans Schmidt',
            'email' => 'hans@example.de',
        ]);
    }

    public function test_test_drive_requires_vehicle_id(): void
    {
        $response = $this->postJson('/api/v1/test-drives', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '+49 172 1234567',
            'preferred_date' => now()->addDays(3)->format('Y-m-d'),
            'preferred_time' => '10:00',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['vehicle_id']);
    }

    public function test_test_drive_requires_name_and_email(): void
    {
        $response = $this->postJson('/api/v1/test-drives', [
            'vehicle_id' => $this->vehicle->id,
            'preferred_date' => now()->addDays(3)->format('Y-m-d'),
            'preferred_time' => '10:00',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email']);
    }

    public function test_authenticated_user_can_list_test_drives(): void
    {
        // Create a test drive request
        $this->postJson('/api/v1/test-drives', [
            'vehicle_id' => $this->vehicle->id,
            'name' => 'Test User',
            'email' => $this->user->email,
            'phone' => '+49 172 1234567',
            'preferred_date' => now()->addDays(3)->format('Y-m-d'),
            'preferred_time' => '14:00',
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/test-drives');

        $response->assertStatus(200);
    }

    public function test_unauthenticated_can_request_test_drive(): void
    {
        // Test drives should be publicly accessible (no auth required for store)
        $response = $this->postJson('/api/v1/test-drives', [
            'vehicle_id' => $this->vehicle->id,
            'name' => 'Guest User',
            'email' => 'guest@example.com',
            'phone' => '+49 172 9999999',
            'preferred_date' => now()->addDays(5)->format('Y-m-d'),
            'preferred_time' => '11:00',
        ]);

        $response->assertStatus(201);
    }

    public function test_test_drive_requires_phone(): void
    {
        $response = $this->postJson('/api/v1/test-drives', [
            'vehicle_id' => $this->vehicle->id,
            'name' => 'Test User',
            'email' => 'test@example.com',
            'preferred_date' => now()->addDays(3)->format('Y-m-d'),
            'preferred_time' => '10:00',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['phone']);
    }
}
