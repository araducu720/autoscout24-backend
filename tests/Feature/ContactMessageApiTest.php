<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vehicle;
use App\Models\ContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Contact Message API Tests
 * Tests the public contact form and admin reply functionality
 */
class ContactMessageApiTest extends TestCase
{
    use RefreshDatabase;

    private Vehicle $vehicle;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\VehicleMakeSeeder::class);
        $this->seed(\Database\Seeders\VehicleModelSeeder::class);

        $seller = User::factory()->create();
        $this->vehicle = Vehicle::factory()->create([
            'user_id' => $seller->id,
            'status' => 'active',
        ]);
    }

    public function test_can_send_contact_message(): void
    {
        $response = $this->postJson('/api/v1/contact-messages', [
            'vehicle_id' => $this->vehicle->id,
            'name' => 'Max Mustermann',
            'email' => 'max@example.de',
            'phone' => '+49 151 12345678',
            'message' => 'Ich interessiere mich für dieses Fahrzeug. Ist es noch verfügbar?',
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('contact_messages', [
            'vehicle_id' => $this->vehicle->id,
            'name' => 'Max Mustermann',
            'email' => 'max@example.de',
            'phone' => '+49 151 12345678',
        ]);
    }

    public function test_contact_message_requires_name_and_email(): void
    {
        $response = $this->postJson('/api/v1/contact-messages', [
            'vehicle_id' => $this->vehicle->id,
            'message' => 'Test message',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email']);
    }

    public function test_contact_message_requires_valid_email(): void
    {
        $response = $this->postJson('/api/v1/contact-messages', [
            'vehicle_id' => $this->vehicle->id,
            'name' => 'Test User',
            'email' => 'invalid-email',
            'message' => 'Test',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_contact_message_requires_vehicle_id(): void
    {
        $response = $this->postJson('/api/v1/contact-messages', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'message' => 'Test message',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['vehicle_id']);
    }

    public function test_contact_message_requires_message(): void
    {
        $response = $this->postJson('/api/v1/contact-messages', [
            'vehicle_id' => $this->vehicle->id,
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['message']);
    }

    public function test_phone_is_optional(): void
    {
        $response = $this->postJson('/api/v1/contact-messages', [
            'vehicle_id' => $this->vehicle->id,
            'name' => 'Test User',
            'email' => 'test@example.com',
            'message' => 'Interested in this vehicle.',
        ]);

        $response->assertStatus(201);
    }

    public function test_contact_message_with_nonexistent_vehicle_fails(): void
    {
        $response = $this->postJson('/api/v1/contact-messages', [
            'vehicle_id' => 99999,
            'name' => 'Test User',
            'email' => 'test@example.com',
            'message' => 'Test message',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['vehicle_id']);
    }
}
