<?php

namespace Tests\Feature;

use App\Models\NotificationPreference;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('test')->plainTextToken;
    }

    public function test_can_get_notifications(): void
    {
        $response = $this->withToken($this->token)->getJson('/api/v1/notifications');
        $response->assertStatus(200);
    }

    public function test_can_get_unread_count(): void
    {
        $response = $this->withToken($this->token)->getJson('/api/v1/notifications/unread-count');

        $response->assertStatus(200)
            ->assertJsonStructure(['count']);
    }

    public function test_can_mark_all_as_read(): void
    {
        $response = $this->withToken($this->token)->putJson('/api/v1/notifications/read-all');
        $response->assertStatus(200);
    }

    public function test_can_get_notification_preferences(): void
    {
        $response = $this->withToken($this->token)->getJson('/api/v1/notifications/preferences');

        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    public function test_can_update_notification_preferences(): void
    {
        $response = $this->withToken($this->token)->putJson('/api/v1/notifications/preferences', [
            'channel' => 'email',
            'marketing' => true,
            'weekly_digest' => false,
        ]);

        $response->assertStatus(200);

        $prefs = NotificationPreference::where('user_id', $this->user->id)
            ->where('channel', 'email')
            ->first();

        $this->assertNotNull($prefs);
        $this->assertTrue($prefs->marketing);
        $this->assertFalse($prefs->weekly_digest);
    }

    public function test_unauthenticated_cannot_access_notifications(): void
    {
        $this->getJson('/api/v1/notifications')->assertStatus(401);
        $this->getJson('/api/v1/notifications/unread-count')->assertStatus(401);
        $this->getJson('/api/v1/notifications/preferences')->assertStatus(401);
    }
}
