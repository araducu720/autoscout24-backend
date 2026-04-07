<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Seller Listing & Analytics API Tests
 * Tests seller vehicle management: listings, inventory stats, analytics,
 * bulk actions, mark sold, promote featured, renew
 */
class SellerListingApiTest extends TestCase
{
    use RefreshDatabase;

    private User $seller;
    private Vehicle $vehicle;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\VehicleMakeSeeder::class);
        $this->seed(\Database\Seeders\VehicleModelSeeder::class);

        $this->seller = User::factory()->create();
        $this->vehicle = Vehicle::factory()->create([
            'user_id' => $this->seller->id,
            'status' => 'active',
            'price' => 28000.00,
        ]);
    }

    // ────────────────── Listings & Stats ──────────────────

    public function test_can_list_seller_vehicles(): void
    {
        Vehicle::factory()->count(3)->create([
            'user_id' => $this->seller->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->seller, 'sanctum')
            ->getJson("/api/v1/sellers/{$this->seller->id}/listings");

        $response->assertStatus(200)
            ->assertJsonStructure(['data', 'meta']);
        $this->assertGreaterThanOrEqual(4, $response->json('meta.total')); // 3 new + 1 setUp
    }

    public function test_can_view_inventory_stats(): void
    {
        Vehicle::factory()->create([
            'user_id' => $this->seller->id,
            'status' => 'sold',
        ]);

        $response = $this->actingAs($this->seller, 'sanctum')
            ->getJson("/api/v1/sellers/{$this->seller->id}/inventory-stats");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'total_listings',
                    'active_listings',
                    'sold_listings',
                    'featured_listings',
                ],
            ]);
    }

    // ────────────────── Analytics ──────────────────

    public function test_owner_can_view_vehicle_analytics(): void
    {
        $response = $this->actingAs($this->seller, 'sanctum')
            ->getJson("/api/v1/vehicles/{$this->vehicle->id}/analytics");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'vehicle_id',
                    'title',
                    'price',
                    'status',
                    'views',
                    'days_listed',
                    'featured',
                ],
            ]);
    }

    public function test_non_owner_cannot_view_vehicle_analytics(): void
    {
        $otherUser = User::factory()->create();

        $response = $this->actingAs($otherUser, 'sanctum')
            ->getJson("/api/v1/vehicles/{$this->vehicle->id}/analytics");

        $response->assertStatus(403);
    }

    // ────────────────── Bulk Actions ──────────────────

    public function test_can_bulk_mark_sold(): void
    {
        $vehicles = Vehicle::factory()->count(2)->create([
            'user_id' => $this->seller->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->seller, 'sanctum')
            ->postJson('/api/v1/listings/bulk-action', [
                'listing_ids' => $vehicles->pluck('id')->toArray(),
                'action' => 'mark_sold',
            ]);

        $response->assertStatus(200);
        foreach ($vehicles as $v) {
            $v->refresh();
            $this->assertEquals('sold', $v->status);
        }
    }

    public function test_cannot_bulk_modify_others_listings(): void
    {
        $otherUser = User::factory()->create();
        $otherVehicle = Vehicle::factory()->create([
            'user_id' => $otherUser->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->seller, 'sanctum')
            ->postJson('/api/v1/listings/bulk-action', [
                'listing_ids' => [$otherVehicle->id],
                'action' => 'mark_sold',
            ]);

        $response->assertStatus(403);
    }

    public function test_bulk_action_validates_action_type(): void
    {
        $response = $this->actingAs($this->seller, 'sanctum')
            ->postJson('/api/v1/listings/bulk-action', [
                'listing_ids' => [$this->vehicle->id],
                'action' => 'invalid_action',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['action']);
    }

    // ────────────────── Single Actions ──────────────────

    public function test_can_mark_vehicle_sold(): void
    {
        $response = $this->actingAs($this->seller, 'sanctum')
            ->postJson("/api/v1/vehicles/{$this->vehicle->id}/mark-sold");

        $response->assertStatus(200);
        $this->vehicle->refresh();
        $this->assertEquals('sold', $this->vehicle->status);
    }

    public function test_can_promote_to_featured(): void
    {
        $response = $this->actingAs($this->seller, 'sanctum')
            ->postJson("/api/v1/vehicles/{$this->vehicle->id}/promote-featured");

        $response->assertStatus(200);
        $this->vehicle->refresh();
        $this->assertTrue((bool) $this->vehicle->is_featured);
    }

    public function test_can_renew_listing(): void
    {
        $response = $this->actingAs($this->seller, 'sanctum')
            ->postJson("/api/v1/vehicles/{$this->vehicle->id}/renew");

        $response->assertStatus(200);
        $this->vehicle->refresh();
        $this->assertEquals('active', $this->vehicle->status);
    }

    public function test_reorder_listings(): void
    {
        $vehicles = Vehicle::factory()->count(3)->create([
            'user_id' => $this->seller->id,
        ]);

        $response = $this->actingAs($this->seller, 'sanctum')
            ->postJson('/api/v1/listings/reorder', [
                'listing_ids' => $vehicles->pluck('id')->toArray(),
            ]);

        $response->assertStatus(200);
    }

    // ────────────────── Auth ──────────────────

    public function test_unauthenticated_cannot_use_seller_actions(): void
    {
        $this->postJson('/api/v1/listings/bulk-action', [
            'listing_ids' => [$this->vehicle->id],
            'action' => 'mark_sold',
        ])->assertStatus(401);
    }
}
