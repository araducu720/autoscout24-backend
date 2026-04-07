<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\SavedSearch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Saved Search API Tests
 * Tests CRUD operations for user saved searches
 */
class SavedSearchApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_create_saved_search(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/saved-searches', [
                'name' => 'BMW 3 Series in Munich',
                'filters' => [
                    'make' => 'BMW',
                    'model' => '3 Series',
                    'city' => 'München',
                    'price_min' => 15000,
                    'price_max' => 35000,
                ],
                'notify' => true,
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('saved_searches', [
            'user_id' => $this->user->id,
            'name' => 'BMW 3 Series in Munich',
        ]);
    }

    public function test_can_list_saved_searches(): void
    {
        SavedSearch::create([
            'user_id' => $this->user->id,
            'name' => 'Search 1',
            'filters' => json_encode(['make' => 'Audi']),
        ]);

        SavedSearch::create([
            'user_id' => $this->user->id,
            'name' => 'Search 2',
            'filters' => json_encode(['make' => 'BMW']),
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/saved-searches');

        $response->assertStatus(200);
    }

    public function test_cannot_see_other_users_searches(): void
    {
        $otherUser = User::factory()->create();
        SavedSearch::create([
            'user_id' => $otherUser->id,
            'name' => 'Other User Search',
            'filters' => json_encode(['make' => 'Mercedes']),
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/saved-searches');

        $response->assertStatus(200);
        // Should not contain the other user's search
        $searches = collect($response->json('data'));
        $this->assertFalse($searches->contains('name', 'Other User Search'));
    }

    public function test_can_update_saved_search(): void
    {
        $search = SavedSearch::create([
            'user_id' => $this->user->id,
            'name' => 'Original Search',
            'filters' => json_encode(['make' => 'Audi']),
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/v1/saved-searches/{$search->id}", [
                'name' => 'Updated Search Name',
            ]);

        $response->assertStatus(200);
        $search->refresh();
        $this->assertEquals('Updated Search Name', $search->name);
    }

    public function test_can_delete_saved_search(): void
    {
        $search = SavedSearch::create([
            'user_id' => $this->user->id,
            'name' => 'Delete Me',
            'filters' => json_encode(['make' => 'Skoda']),
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/v1/saved-searches/{$search->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('saved_searches', ['id' => $search->id]);
    }

    public function test_unauthenticated_cannot_access(): void
    {
        $this->getJson('/api/v1/saved-searches')->assertStatus(401);
        $this->postJson('/api/v1/saved-searches')->assertStatus(401);
    }
}
