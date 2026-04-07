<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vehicle;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Order API Flow Tests
 * Tests direct purchase order lifecycle: create → accept/reject → cancel
 */
class OrderApiTest extends TestCase
{
    use RefreshDatabase;

    private User $buyer;
    private User $seller;
    private Vehicle $vehicle;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\VehicleMakeSeeder::class);
        $this->seed(\Database\Seeders\VehicleModelSeeder::class);

        $this->buyer = User::factory()->create(['name' => 'Buyer']);
        $this->seller = User::factory()->create(['name' => 'Seller']);
        $this->vehicle = Vehicle::factory()->create([
            'user_id' => $this->seller->id,
            'price' => 25000.00,
            'status' => 'active',
        ]);
    }

    public function test_buyer_can_create_order(): void
    {
        $response = $this->actingAs($this->buyer, 'sanctum')
            ->postJson('/api/v1/orders', [
                'vehicle_id' => $this->vehicle->id,
                'message' => 'I would like to buy this vehicle.',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['id', 'status', 'total_price'],
            ]);

        $order = Order::first();
        $this->assertEquals('pending', $order->status);
        $this->assertStringStartsWith('ORD-', $order->order_number);
        $this->assertEquals($this->buyer->id, $order->buyer_id);
        $this->assertEquals($this->seller->id, $order->seller_id);
        $this->assertNotNull($order->payment_deadline);
    }

    public function test_buyer_cannot_order_own_vehicle(): void
    {
        $response = $this->actingAs($this->seller, 'sanctum')
            ->postJson('/api/v1/orders', [
                'vehicle_id' => $this->vehicle->id,
            ]);

        $response->assertStatus(422);
    }

    public function test_seller_can_accept_order(): void
    {
        $this->actingAs($this->buyer, 'sanctum')
            ->postJson('/api/v1/orders', ['vehicle_id' => $this->vehicle->id]);

        $order = Order::first();

        $response = $this->actingAs($this->seller, 'sanctum')
            ->postJson("/api/v1/orders/{$order->id}/accept");

        $response->assertStatus(200);
        $order->refresh();
        $this->assertEquals('accepted', $order->status);
        $this->assertNotNull($order->accepted_at);
    }

    public function test_seller_can_reject_order(): void
    {
        $this->actingAs($this->buyer, 'sanctum')
            ->postJson('/api/v1/orders', ['vehicle_id' => $this->vehicle->id]);

        $order = Order::first();

        $response = $this->actingAs($this->seller, 'sanctum')
            ->postJson("/api/v1/orders/{$order->id}/reject", [
                'reason' => 'Vehicle is reserved for another buyer.',
            ]);

        $response->assertStatus(200);
        $order->refresh();
        $this->assertEquals('rejected', $order->status);
    }

    public function test_buyer_cannot_accept_order(): void
    {
        $this->actingAs($this->buyer, 'sanctum')
            ->postJson('/api/v1/orders', ['vehicle_id' => $this->vehicle->id]);

        $order = Order::first();

        $response = $this->actingAs($this->buyer, 'sanctum')
            ->postJson("/api/v1/orders/{$order->id}/accept");

        $response->assertStatus(403);
    }

    public function test_can_cancel_order(): void
    {
        $this->actingAs($this->buyer, 'sanctum')
            ->postJson('/api/v1/orders', ['vehicle_id' => $this->vehicle->id]);

        $order = Order::first();

        $response = $this->actingAs($this->buyer, 'sanctum')
            ->postJson("/api/v1/orders/{$order->id}/cancel");

        $response->assertStatus(200);
        $order->refresh();
        $this->assertEquals('cancelled', $order->status);
    }

    public function test_can_list_orders(): void
    {
        $this->actingAs($this->buyer, 'sanctum')
            ->postJson('/api/v1/orders', ['vehicle_id' => $this->vehicle->id]);

        $response = $this->actingAs($this->buyer, 'sanctum')
            ->getJson('/api/v1/orders');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_can_view_single_order(): void
    {
        $this->actingAs($this->buyer, 'sanctum')
            ->postJson('/api/v1/orders', ['vehicle_id' => $this->vehicle->id]);

        $order = Order::first();

        $response = $this->actingAs($this->buyer, 'sanctum')
            ->getJson("/api/v1/orders/{$order->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $order->id);
    }

    public function test_unrelated_user_cannot_view_order(): void
    {
        $this->actingAs($this->buyer, 'sanctum')
            ->postJson('/api/v1/orders', ['vehicle_id' => $this->vehicle->id]);

        $order = Order::first();
        $otherUser = User::factory()->create();

        $response = $this->actingAs($otherUser, 'sanctum')
            ->getJson("/api/v1/orders/{$order->id}");

        // The controller uses findOrFail with user scope, returns 404 for unrelated users
        $response->assertStatus(404);
    }

    public function test_order_requires_vehicle_id(): void
    {
        $response = $this->actingAs($this->buyer, 'sanctum')
            ->postJson('/api/v1/orders', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['vehicle_id']);
    }

    public function test_unauthenticated_cannot_create_order(): void
    {
        $response = $this->postJson('/api/v1/orders', [
            'vehicle_id' => $this->vehicle->id,
        ]);

        $response->assertStatus(401);
    }
}
