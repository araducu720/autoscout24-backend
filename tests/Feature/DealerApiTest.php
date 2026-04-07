<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Dealer;
use App\Models\VehicleMake;
use App\Models\VehicleModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Dealer API Tests
 * Tests dealer registration, profile management, listing access, and statistics
 */
class DealerApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private VehicleMake $make;
    private VehicleModel $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\VehicleMakeSeeder::class);
        $this->seed(\Database\Seeders\VehicleModelSeeder::class);

        $this->user = User::factory()->create();
        $this->make = VehicleMake::first();
        $this->model = VehicleModel::where('make_id', $this->make->id)->first();
    }

    private function dealerPayload(array $overrides = []): array
    {
        return array_merge([
            'company_name' => 'AutoHaus München GmbH',
            'address' => 'Leopoldstraße 42',
            'city' => 'München',
            'postal_code' => '80802',
            'country' => 'Germany',
            'phone' => '+49 89 1234567',
            'email' => 'info@autohaus-muenchen.de',
            'website' => 'https://autohaus-muenchen.de',
            'tax_id' => 'DE123456789',
            'type' => 'independent',
            'description' => 'Premium used car dealer specializing in German luxury brands.',
            'offers_home_delivery' => true,
            'offers_financing' => true,
            'offers_warranty' => true,
        ], $overrides);
    }

    // ────────────────── Registration ──────────────────

    public function test_user_can_register_as_dealer(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/dealer/register', $this->dealerPayload());

        $response->assertStatus(201)
            ->assertJsonPath('data.company_name', 'AutoHaus München GmbH')
            ->assertJsonPath('data.is_verified', false)
            ->assertJsonPath('data.is_active', true);

        $this->assertDatabaseHas('dealers', [
            'user_id' => $this->user->id,
            'company_name' => 'AutoHaus München GmbH',
        ]);
    }

    public function test_cannot_register_twice(): void
    {
        Dealer::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/dealer/register', $this->dealerPayload());

        $response->assertStatus(422);
    }

    public function test_registration_requires_company_name(): void
    {
        $payload = $this->dealerPayload();
        unset($payload['company_name']);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/dealer/register', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['company_name']);
    }

    public function test_registration_validates_type(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/dealer/register', $this->dealerPayload([
                'type' => 'invalid_type',
            ]));

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['type']);
    }

    // ────────────────── Profile ──────────────────

    public function test_dealer_can_view_profile(): void
    {
        Dealer::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/dealer/profile');

        $response->assertStatus(200)
            ->assertJsonPath('is_dealer', true);
    }

    public function test_non_dealer_gets_404(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/dealer/profile');

        $response->assertStatus(404)
            ->assertJsonPath('is_dealer', false);
    }

    public function test_dealer_can_update_profile(): void
    {
        Dealer::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson('/api/v1/dealer/profile', [
                'company_name' => 'Updated AutoHaus GmbH',
                'offers_home_delivery' => false,
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('dealers', [
            'user_id' => $this->user->id,
            'company_name' => 'Updated AutoHaus GmbH',
        ]);
    }

    // ────────────────── Statistics ──────────────────

    public function test_dealer_can_view_statistics(): void
    {
        Dealer::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/dealer/statistics');

        $response->assertStatus(200);
    }

    // ────────────────── Auth ──────────────────

    public function test_unauthenticated_cannot_register_dealer(): void
    {
        $response = $this->postJson('/api/v1/dealer/register', $this->dealerPayload());
        $response->assertStatus(401);
    }
}
