<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Dashboard API Tests
 * Tests user dashboard statistics and activity summary endpoints
 */
class DashboardApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\VehicleMakeSeeder::class);
        $this->seed(\Database\Seeders\VehicleModelSeeder::class);

        $this->user = User::factory()->create();
    }

    public function test_can_view_dashboard_stats(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/dashboard/stats');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'total_sales',
                    'earnings',
                    'monthly_sales',
                    'total_purchases',
                    'saved_listings',
                    'unread_notifications',
                ],
            ]);
    }

    public function test_dashboard_stats_show_zero_for_new_user(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/dashboard/stats');

        $response->assertStatus(200)
            ->assertJsonPath('data.total_sales', 0)
            ->assertJsonPath('data.total_purchases', 0)
            ->assertJsonPath('data.earnings', 0);
    }

    public function test_can_view_activity(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/dashboard/activity');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'total_transactions',
                    'pending_transactions',
                ],
            ]);
    }

    public function test_unauthenticated_cannot_view_dashboard(): void
    {
        $this->getJson('/api/v1/dashboard/stats')->assertStatus(401);
        $this->getJson('/api/v1/dashboard/activity')->assertStatus(401);
    }
}
