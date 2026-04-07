<?php

namespace Tests\Feature\SafeTrade;

use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleMake;
use App\Models\VehicleModel;
use App\Models\SafetradeTransaction;
use App\Models\EscrowTransaction;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\TransactionTimeline;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Complete SafeTrade E2E Flow Test
 * 
 * Tests the entire purchase flow from start to finish:
 * Buyer creates transaction → Funds escrow → Seller ships → 
 * Buyer confirms delivery → Funds released to seller
 */
class SafeTradeCompleteFlowTest extends TestCase
{
    use RefreshDatabase;

    private User $buyer;
    private User $seller;
    private Vehicle $vehicle;
    private string $buyerToken;
    private string $sellerToken;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\VehicleMakeSeeder::class);
        $this->seed(\Database\Seeders\VehicleModelSeeder::class);

        // Create buyer and seller
        $this->buyer = User::factory()->create([
            'name' => 'Hans Buyer',
            'email' => 'buyer@test.com',
        ]);
        $this->seller = User::factory()->create([
            'name' => 'Maria Seller',
            'email' => 'seller@test.com',
        ]);

        // Create vehicle owned by seller
        $this->vehicle = Vehicle::factory()->create([
            'user_id' => $this->seller->id,
            'title' => 'BMW 320d xDrive 2023',
            'price' => 35000.00,
            'status' => 'active',
        ]);

        // Get auth tokens
        $this->buyerToken = $this->buyer->createToken('test')->plainTextToken;
        $this->sellerToken = $this->seller->createToken('test')->plainTextToken;
    }

    // ─── STEP 1: Create SafeTrade Transaction ─────────────────

    public function test_buyer_can_create_safetrade_transaction(): void
    {
        $response = $this->actingAs($this->buyer, 'sanctum')
            ->postJson('/api/v1/transactions', [
                'vehicle_id' => $this->vehicle->id,
                'message' => 'I want to buy this BMW. When can I expect delivery?',
                'delivery_method' => 'shipping',
                'delivery_address' => 'Hauptstraße 42, 10115 Berlin, Germany',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'reference',
                    'status',
                    'escrow_status',
                    'amount',
                    'escrow_fee',
                    'vehicle_title',
                    'vehicle_price',
                    'delivery_method',
                ],
            ]);

        $data = $response->json('data');

        // Verify transaction created correctly
        $this->assertEquals('pending', $data['status']);
        $this->assertEquals('pending', $data['escrow_status']);
        $this->assertStringStartsWith('AS24-ST-', $data['reference']);
        $this->assertEquals('shipping', $data['delivery_method']);
        $this->assertEquals('BMW 320d xDrive 2023', $data['vehicle_title']);

        // Verify escrow fee is 1.5%
        $expectedFee = round(35000 * 0.015, 2);
        $this->assertEquals($expectedFee, (float)$data['escrow_fee']);
        $this->assertEquals(35000 + $expectedFee, (float)$data['amount']);

        // Verify related records were created
        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseCount('safetrade_transactions', 1);
        $this->assertDatabaseCount('escrow_transactions', 1);
        $this->assertDatabaseCount('invoices', 1);

        // Verify order was auto-accepted
        $order = Order::first();
        $this->assertEquals('accepted', $order->status);
        $this->assertNotNull($order->accepted_at);

        // Verify invoice
        $invoice = Invoice::first();
        $this->assertStringStartsWith('INV-', $invoice->invoice_number);
        $this->assertEquals(35000, (float)$invoice->amount);
    }

    public function test_buyer_cannot_buy_own_vehicle(): void
    {
        $response = $this->actingAs($this->seller, 'sanctum')
            ->postJson('/api/v1/transactions', [
                'vehicle_id' => $this->vehicle->id,
                'delivery_method' => 'pickup',
            ]);

        $response->assertStatus(422);
    }

    public function test_cannot_buy_inactive_vehicle(): void
    {
        $this->vehicle->update(['status' => 'sold']);

        $response = $this->actingAs($this->buyer, 'sanctum')
            ->postJson('/api/v1/transactions', [
                'vehicle_id' => $this->vehicle->id,
                'delivery_method' => 'pickup',
            ]);

        $response->assertStatus(422);
    }

    public function test_unauthenticated_cannot_create_transaction(): void
    {
        $response = $this->postJson('/api/v1/transactions', [
            'vehicle_id' => $this->vehicle->id,
            'delivery_method' => 'pickup',
        ]);

        $response->assertStatus(401);
    }

    // ─── STEP 2: Fund Escrow ──────────────────────────────────

    public function test_buyer_can_fund_escrow(): void
    {
        // Create transaction first
        $this->actingAs($this->buyer, 'sanctum')
            ->postJson('/api/v1/transactions', [
                'vehicle_id' => $this->vehicle->id,
                'delivery_method' => 'pickup',
            ]);

        $transaction = SafetradeTransaction::first();

        // Fund escrow
        $response = $this->actingAs($this->buyer, 'sanctum')
            ->postJson("/api/v1/escrow/{$transaction->id}/fund", [
                'payment_method' => 'bank_transfer',
            ]);

        $response->assertStatus(200);

        // Verify statuses updated
        $transaction->refresh();
        $this->assertEquals('funded', $transaction->escrow_status);
        $this->assertNotNull($transaction->funded_at);

        $escrow = EscrowTransaction::first();
        $this->assertEquals('funded', $escrow->status);
        $this->assertNotNull($escrow->funded_at);
    }

    public function test_seller_cannot_fund_escrow(): void
    {
        $this->actingAs($this->buyer, 'sanctum')
            ->postJson('/api/v1/transactions', [
                'vehicle_id' => $this->vehicle->id,
                'delivery_method' => 'pickup',
            ]);

        $transaction = SafetradeTransaction::first();

        $response = $this->actingAs($this->seller, 'sanctum')
            ->postJson("/api/v1/escrow/{$transaction->id}/fund", [
                'payment_method' => 'bank_transfer',
            ]);

        $response->assertStatus(403);
    }

    // ─── STEP 3: Update Status & Tracking ─────────────────────

    public function test_can_update_transaction_status(): void
    {
        $this->createFundedTransaction();
        $transaction = SafetradeTransaction::first();

        // Seller confirms and ships
        $response = $this->actingAs($this->seller, 'sanctum')
            ->putJson("/api/v1/transactions/{$transaction->id}/status", [
                'status' => 'confirmed',
            ]);

        $response->assertStatus(200);

        $transaction->refresh();
        $this->assertEquals('confirmed', $transaction->status);
    }

    public function test_can_add_tracking_number(): void
    {
        $this->createFundedTransaction();
        $transaction = SafetradeTransaction::first();

        $response = $this->actingAs($this->seller, 'sanctum')
            ->putJson("/api/v1/transactions/{$transaction->id}/tracking", [
                'tracking_number' => 'DHL-DE-123456789',
            ]);

        $response->assertStatus(200);

        $transaction->refresh();
        $this->assertEquals('DHL-DE-123456789', $transaction->tracking_number);
    }

    // ─── STEP 4: Confirm Delivery ─────────────────────────────

    public function test_buyer_can_confirm_delivery(): void
    {
        $this->createFundedTransaction();
        $transaction = SafetradeTransaction::first();

        // Set status to in_transit first
        $transaction->update(['status' => 'in_transit']);

        // Buyer confirms delivery
        $response = $this->actingAs($this->buyer, 'sanctum')
            ->postJson("/api/v1/transactions/{$transaction->id}/confirm-delivery");

        $response->assertStatus(200);

        $transaction->refresh();
        // confirmReceipt → delivered → tryReleaseFunds → auto-completes
        $this->assertEquals('completed', $transaction->status);
        $this->assertNotNull($transaction->delivered_at);
        $this->assertNotNull($transaction->completed_at);
    }

    public function test_seller_cannot_confirm_delivery(): void
    {
        $this->createFundedTransaction();
        $transaction = SafetradeTransaction::first();
        $transaction->update(['status' => 'in_transit']);

        $response = $this->actingAs($this->seller, 'sanctum')
            ->postJson("/api/v1/transactions/{$transaction->id}/confirm-delivery");

        $response->assertStatus(403);
    }

    // ─── STEP 5: Release Funds ────────────────────────────────

    public function test_buyer_can_release_funds(): void
    {
        $this->createFundedTransaction();
        $transaction = SafetradeTransaction::first();
        $transaction->update(['status' => 'delivered', 'delivered_at' => now()]);

        // Release funds
        $response = $this->actingAs($this->buyer, 'sanctum')
            ->postJson("/api/v1/escrow/{$transaction->id}/release");

        $response->assertStatus(200);

        $transaction->refresh();
        $this->assertEquals('released', $transaction->escrow_status);

        $escrow = EscrowTransaction::first();
        $this->assertEquals('released', $escrow->status);
        $this->assertNotNull($escrow->released_at);
    }

    // ─── STEP 6: Complete Transaction ─────────────────────────

    public function test_complete_transaction(): void
    {
        $this->createFundedTransaction();
        $transaction = SafetradeTransaction::first();
        $transaction->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);

        $response = $this->actingAs($this->buyer, 'sanctum')
            ->postJson("/api/v1/transactions/{$transaction->id}/complete");

        $response->assertStatus(200);

        $transaction->refresh();
        $this->assertEquals('completed', $transaction->status);
        $this->assertNotNull($transaction->completed_at);
    }

    // ─── FULL E2E: Happy Path ─────────────────────────────────

    public function test_complete_safetrade_happy_path(): void
    {
        // 1. Buyer creates transaction
        $response = $this->actingAs($this->buyer, 'sanctum')
            ->postJson('/api/v1/transactions', [
                'vehicle_id' => $this->vehicle->id,
                'message' => 'I want to purchase this vehicle.',
                'delivery_method' => 'shipping',
                'delivery_address' => 'Berliner Str. 10, 10115 Berlin',
            ]);
        $response->assertStatus(201);
        $transactionId = $response->json('data.id');

        // 2. Buyer funds escrow
        $response = $this->actingAs($this->buyer, 'sanctum')
            ->postJson("/api/v1/escrow/{$transactionId}/fund", [
                'payment_method' => 'bank_transfer',
            ]);
        $response->assertStatus(200);

        // 3. Seller confirms the order
        $response = $this->actingAs($this->seller, 'sanctum')
            ->putJson("/api/v1/transactions/{$transactionId}/status", [
                'status' => 'confirmed',
            ]);
        $response->assertStatus(200);

        // 4. Seller ships with tracking
        $response = $this->actingAs($this->seller, 'sanctum')
            ->putJson("/api/v1/transactions/{$transactionId}/tracking", [
                'tracking_number' => 'DHL-DE-987654321',
            ]);
        $response->assertStatus(200);

        // 5. Update to in_transit
        $response = $this->actingAs($this->seller, 'sanctum')
            ->putJson("/api/v1/transactions/{$transactionId}/status", [
                'status' => 'in_transit',
            ]);
        $response->assertStatus(200);

        // 6. Buyer confirms delivery → auto-releases funds → auto-completes
        $response = $this->actingAs($this->buyer, 'sanctum')
            ->postJson("/api/v1/transactions/{$transactionId}/confirm-delivery");
        $response->assertStatus(200);

        // ── Verify final state ──
        // confirmReceipt() → delivered → tryReleaseFunds() → auto-completes
        $transaction = SafetradeTransaction::find($transactionId);
        $this->assertEquals('completed', $transaction->status);
        $this->assertEquals('released', $transaction->escrow_status);
        $this->assertEquals('completed', $transaction->payment_status);
        $this->assertNotNull($transaction->funded_at);
        $this->assertNotNull($transaction->confirmed_at);
        $this->assertNotNull($transaction->delivered_at);
        $this->assertNotNull($transaction->completed_at);
        $this->assertEquals('DHL-DE-987654321', $transaction->tracking_number);

        // Verify escrow
        $escrow = EscrowTransaction::where('safetrade_transaction_id', $transactionId)->first();
        $this->assertEquals('released', $escrow->status);
        $this->assertNotNull($escrow->released_at);

        // Verify invoice exists
        $invoice = Invoice::where('safetrade_transaction_id', $transactionId)->first();
        $this->assertNotNull($invoice);

        // Verify timeline has events
        $timelineCount = TransactionTimeline::where('safetrade_transaction_id', $transactionId)->count();
        $this->assertGreaterThan(0, $timelineCount);
    }

    // ─── CANCELLATION FLOW ────────────────────────────────────

    public function test_buyer_can_cancel_transaction(): void
    {
        $this->actingAs($this->buyer, 'sanctum')
            ->postJson('/api/v1/transactions', [
                'vehicle_id' => $this->vehicle->id,
                'delivery_method' => 'pickup',
            ]);

        $transaction = SafetradeTransaction::first();

        $response = $this->actingAs($this->buyer, 'sanctum')
            ->postJson("/api/v1/transactions/{$transaction->id}/cancel", [
                'reason' => 'Changed my mind about the purchase.',
            ]);

        $response->assertStatus(200);

        $transaction->refresh();
        $this->assertEquals('cancelled', $transaction->status);
        $this->assertNotNull($transaction->cancelled_at);
    }

    // ─── DISPUTE FLOW ─────────────────────────────────────────

    public function test_buyer_can_open_dispute(): void
    {
        $this->createFundedTransaction();
        $transaction = SafetradeTransaction::first();

        $response = $this->actingAs($this->buyer, 'sanctum')
            ->postJson("/api/v1/escrow/{$transaction->id}/dispute", [
                'reason' => 'Vehicle has undisclosed damage on the front bumper.',
                'evidence' => ['Photo of damage sent via email'],
            ]);

        $response->assertStatus(200);

        $transaction->refresh();
        $this->assertEquals('disputed', $transaction->escrow_status);

        $escrow = EscrowTransaction::first();
        $this->assertEquals('disputed', $escrow->status);
        $this->assertEquals('Vehicle has undisclosed damage on the front bumper.', $escrow->dispute_reason);
    }

    public function test_seller_can_open_dispute(): void
    {
        $this->createFundedTransaction();
        $transaction = SafetradeTransaction::first();

        $response = $this->actingAs($this->seller, 'sanctum')
            ->postJson("/api/v1/escrow/{$transaction->id}/dispute", [
                'reason' => 'Buyer claims damage that was documented in listing.',
            ]);

        $response->assertStatus(200);
    }

    public function test_dispute_can_be_resolved(): void
    {
        $this->createFundedTransaction();
        $transaction = SafetradeTransaction::first();

        // Open dispute
        $this->actingAs($this->buyer, 'sanctum')
            ->postJson("/api/v1/escrow/{$transaction->id}/dispute", [
                'reason' => 'Vehicle condition not as described.',
            ]);

        $escrow = EscrowTransaction::first();

        // Resolve dispute (admin would do this)
        $response = $this->actingAs($this->buyer, 'sanctum')
            ->postJson("/api/v1/escrow/{$escrow->id}/resolve", [
                'resolution' => 'buyer',
            ]);

        $response->assertStatus(200);
    }

    // ─── LISTING & DETAILS ────────────────────────────────────

    public function test_buyer_can_list_their_transactions(): void
    {
        $this->createFundedTransaction();

        $response = $this->actingAs($this->buyer, 'sanctum')
            ->getJson('/api/v1/transactions');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'reference', 'status', 'vehicle_title', 'amount'],
                ],
            ]);

        $this->assertCount(1, $response->json('data'));
    }

    public function test_can_get_transaction_details(): void
    {
        $this->createFundedTransaction();
        $transaction = SafetradeTransaction::first();

        $response = $this->actingAs($this->buyer, 'sanctum')
            ->getJson("/api/v1/transactions/{$transaction->id}/details");

        $response->assertStatus(200);
    }

    public function test_unrelated_user_cannot_see_transaction(): void
    {
        $this->createFundedTransaction();
        $transaction = SafetradeTransaction::first();

        $otherUser = User::factory()->create();

        $response = $this->actingAs($otherUser, 'sanctum')
            ->getJson("/api/v1/transactions/{$transaction->id}");

        $response->assertStatus(404);
    }

    // ─── Helpers ──────────────────────────────────────────────

    private function createFundedTransaction(): void
    {
        $this->actingAs($this->buyer, 'sanctum')
            ->postJson('/api/v1/transactions', [
                'vehicle_id' => $this->vehicle->id,
                'delivery_method' => 'pickup',
            ]);

        $transaction = SafetradeTransaction::first();

        $this->actingAs($this->buyer, 'sanctum')
            ->postJson("/api/v1/escrow/{$transaction->id}/fund", [
                'payment_method' => 'bank_transfer',
            ]);
    }
}
