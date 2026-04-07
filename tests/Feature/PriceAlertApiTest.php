<?php

namespace Tests\Feature;

use App\Models\PriceAlert;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PriceAlertApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Vehicle $vehicle;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('test')->plainTextToken;
        $this->vehicle = Vehicle::factory()->create(['price' => 25000]);
    }

    public function test_user_can_create_price_alert(): void
    {
        $response = $this->withToken($this->token)->postJson('/api/v1/price-alerts', [
            'vehicle_id' => $this->vehicle->id,
            'target_price' => 20000,
            'alert_type' => 'below',
            'notify_email' => true,
            'notify_push' => true,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('message', 'Price alert created successfully')
            ->assertJsonPath('data.target_price', '20000.00')
            ->assertJsonPath('data.alert_type', 'below');

        $this->assertDatabaseHas('price_alerts', [
            'user_id' => $this->user->id,
            'vehicle_id' => $this->vehicle->id,
            'target_price' => 20000,
        ]);
    }

    public function test_user_can_list_price_alerts(): void
    {
        PriceAlert::factory()->count(3)->create(['user_id' => $this->user->id]);

        $response = $this->withToken($this->token)->getJson('/api/v1/price-alerts');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_user_can_update_price_alert(): void
    {
        $alert = PriceAlert::factory()->create([
            'user_id' => $this->user->id,
            'is_active' => true,
        ]);

        $response = $this->withToken($this->token)->putJson("/api/v1/price-alerts/{$alert->id}", [
            'is_active' => false,
        ]);

        $response->assertStatus(200);
        $this->assertFalse($alert->fresh()->is_active);
    }

    public function test_user_can_delete_price_alert(): void
    {
        $alert = PriceAlert::factory()->create(['user_id' => $this->user->id]);

        $response = $this->withToken($this->token)->deleteJson("/api/v1/price-alerts/{$alert->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('price_alerts', ['id' => $alert->id]);
    }

    public function test_user_cannot_modify_other_users_alert(): void
    {
        $otherUser = User::factory()->create();
        $alert = PriceAlert::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->withToken($this->token)->putJson("/api/v1/price-alerts/{$alert->id}", [
            'is_active' => false,
        ]);

        $response->assertStatus(403);
    }

    public function test_price_alert_validation(): void
    {
        $response = $this->withToken($this->token)->postJson('/api/v1/price-alerts', [
            'target_price' => -100,
            'alert_type' => 'invalid_type',
        ]);

        $response->assertStatus(422);
    }

    public function test_unauthenticated_user_cannot_access_alerts(): void
    {
        $response = $this->getJson('/api/v1/price-alerts');
        $response->assertStatus(401);
    }
}
