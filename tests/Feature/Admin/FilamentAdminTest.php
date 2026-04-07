<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Vehicle;
use App\Models\ContactMessage;
use App\Models\Order;
use App\Models\SafetradeTransaction;
use App\Models\Dealer;
use App\Models\VehicleMake;
use App\Models\VehicleModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Filament Admin Panel Tests
 * Tests admin panel access control and basic resource operations
 */
class FilamentAdminTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $normalUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\VehicleMakeSeeder::class);
        $this->seed(\Database\Seeders\VehicleModelSeeder::class);

        $this->admin = User::factory()->create([
            'is_admin' => true,
            'name' => 'Admin User',
            'email' => 'admin@autoscout24safetrade.com',
        ]);

        $this->normalUser = User::factory()->create([
            'is_admin' => false,
            'name' => 'Normal User',
        ]);
    }

    // ────────────────── Access Control ──────────────────

    public function test_admin_can_access_panel(): void
    {
        $this->assertTrue($this->admin->canAccessPanel(
            \Filament\Facades\Filament::getPanel('admin')
        ));
    }

    public function test_normal_user_cannot_access_panel(): void
    {
        $this->assertFalse($this->normalUser->canAccessPanel(
            \Filament\Facades\Filament::getPanel('admin')
        ));
    }

    public function test_admin_login_page_is_accessible(): void
    {
        $response = $this->get('/admin/login');
        $response->assertStatus(200);
    }

    public function test_unauthenticated_redirects_from_admin(): void
    {
        $response = $this->get('/admin');
        // Should redirect to login
        $response->assertRedirect();
    }

    // ────────────────── Vehicle Resource ──────────────────

    public function test_admin_can_list_vehicles(): void
    {
        Vehicle::factory()->count(5)->create([
            'user_id' => $this->normalUser->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get('/admin/vehicles');

        $response->assertStatus(200);
    }

    // ────────────────── User Resource ──────────────────

    public function test_admin_can_list_users(): void
    {
        $response = $this->actingAs($this->admin)
            ->get('/admin/users');

        $response->assertStatus(200);
    }

    // ────────────────── Contact Messages ──────────────────

    public function test_admin_can_list_contact_messages(): void
    {
        $vehicle = Vehicle::factory()->create(['user_id' => $this->normalUser->id]);
        ContactMessage::create([
            'vehicle_id' => $vehicle->id,
            'name' => 'Test Customer',
            'email' => 'customer@example.com',
            'message' => 'Is this vehicle still available?',
        ]);

        $response = $this->actingAs($this->admin)
            ->get('/admin/contact-messages');

        $response->assertStatus(200);
    }

    // ────────────────── Orders ──────────────────

    public function test_admin_can_list_orders(): void
    {
        $response = $this->actingAs($this->admin)
            ->get('/admin/orders');

        $response->assertStatus(200);
    }

    // ────────────────── SafeTrade Transactions ──────────────────

    public function test_admin_can_list_safetrade_transactions(): void
    {
        $response = $this->actingAs($this->admin)
            ->get('/admin/safetrade-transactions');

        $response->assertStatus(200);
    }

    // ────────────────── Dealers ──────────────────

    public function test_admin_can_list_dealers(): void
    {
        Dealer::factory()->create(['user_id' => $this->normalUser->id]);

        $response = $this->actingAs($this->admin)
            ->get('/admin/dealers');

        $response->assertStatus(200);
    }

    // ────────────────── Vehicle Makes & Models ──────────────────

    public function test_admin_can_list_vehicle_makes(): void
    {
        $response = $this->actingAs($this->admin)
            ->get('/admin/vehicle-makes');

        $response->assertStatus(200);
    }

    public function test_admin_can_list_vehicle_models(): void
    {
        $response = $this->actingAs($this->admin)
            ->get('/admin/vehicle-models');

        $response->assertStatus(200);
    }

    // ────────────────── Invoices ──────────────────

    public function test_admin_can_list_invoices(): void
    {
        $response = $this->actingAs($this->admin)
            ->get('/admin/invoices');

        $response->assertStatus(200);
    }

    // ────────────────── Escrow Transactions ──────────────────

    public function test_admin_can_list_escrow_transactions(): void
    {
        $response = $this->actingAs($this->admin)
            ->get('/admin/escrow-transactions');

        $response->assertStatus(200);
    }

    // ────────────────── Audit Logs ──────────────────

    public function test_admin_can_list_audit_logs(): void
    {
        $response = $this->actingAs($this->admin)
            ->get('/admin/audit-logs');

        $response->assertStatus(200);
    }

    // ────────────────── Test Drive Requests ──────────────────

    public function test_admin_can_list_test_drive_requests(): void
    {
        $response = $this->actingAs($this->admin)
            ->get('/admin/test-drive-requests');

        $response->assertStatus(200);
    }

    // ────────────────── Dashboard Widgets ──────────────────

    public function test_admin_dashboard_loads(): void
    {
        $response = $this->actingAs($this->admin)
            ->get('/admin');

        $response->assertStatus(200);
    }
}
