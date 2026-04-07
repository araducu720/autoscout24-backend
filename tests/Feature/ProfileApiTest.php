<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Profile & Bank Details API Tests
 * Tests user profile CRUD, bank details with IBAN validation, and preferences
 */
class ProfileApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'name' => 'Laura Meier',
            'locale' => 'de',
            'currency' => 'EUR',
            'country' => 'DE',
        ]);
    }

    // ────────────────── Profile CRUD ──────────────────

    public function test_user_can_view_profile(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/profile');

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Laura Meier')
            ->assertJsonPath('data.locale', 'de')
            ->assertJsonPath('data.currency', 'EUR')
            ->assertJsonPath('data.has_bank_details', false);
    }

    public function test_user_can_update_profile(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson('/api/v1/profile', [
                'name' => 'Laura Schmidt',
                'phone' => '+49 151 98765432',
            ]);

        $response->assertStatus(200);
        $this->user->refresh();
        $this->assertEquals('Laura Schmidt', $this->user->name);
        $this->assertEquals('+49 151 98765432', $this->user->phone);
    }

    public function test_profile_validates_locale(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson('/api/v1/profile', [
                'locale' => 'zz',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['locale']);
    }

    public function test_profile_validates_currency(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson('/api/v1/profile', [
                'currency' => 'USD',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['currency']);
    }

    // ────────────────── Bank Details ──────────────────

    public function test_user_can_set_bank_details(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson('/api/v1/profile/bank-details', [
                'bank_name' => 'Deutsche Bank',
                'iban' => 'DE89370400440532013000',
                'bic' => 'COBADEFFXXX',
                'account_holder' => 'Laura Meier',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.bank_name', 'Deutsche Bank')
            ->assertJsonPath('data.account_holder', 'Laura Meier');
    }

    public function test_iban_validation_rejects_invalid_format(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson('/api/v1/profile/bank-details', [
                'bank_name' => 'Test Bank',
                'iban' => 'invalid-iban-123',
                'account_holder' => 'Test User',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['iban']);
    }

    public function test_bic_validation_rejects_invalid_format(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson('/api/v1/profile/bank-details', [
                'bank_name' => 'Test Bank',
                'iban' => 'DE89370400440532013000',
                'bic' => 'invalid-bic',
                'account_holder' => 'Test User',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['bic']);
    }

    public function test_bank_details_requires_bank_name_and_account_holder(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson('/api/v1/profile/bank-details', [
                'iban' => 'DE89370400440532013000',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['bank_name', 'account_holder']);
    }

    public function test_can_get_bank_details(): void
    {
        // First set bank details
        $this->user->update([
            'bank_name' => 'Deutsche Bank',
            'iban' => 'DE89370400440532013000',
            'bic' => 'COBADEFFXXX',
            'account_holder' => 'Laura Meier',
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/profile/bank-details');

        $response->assertStatus(200)
            ->assertJsonPath('has_bank_details', true)
            ->assertJsonPath('data.bank_name', 'Deutsche Bank');
    }

    public function test_get_bank_details_when_none_set(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/profile/bank-details');

        $response->assertStatus(200)
            ->assertJsonPath('has_bank_details', false);
    }

    // ────────────────── Preferences ──────────────────

    public function test_can_update_preferences(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson('/api/v1/profile/preferences', [
                'locale' => 'fr',
                'currency' => 'CHF',
                'country' => 'CH',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.locale', 'fr')
            ->assertJsonPath('data.currency', 'CHF')
            ->assertJsonPath('data.country', 'CH');
    }

    // ────────────────── Change Password ──────────────────

    public function test_can_change_password(): void
    {
        $this->user->update(['password' => Hash::make('OldPassword123')]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/profile/change-password', [
                'current_password' => 'OldPassword123',
                'new_password' => 'NewSecurePass456',
                'new_password_confirmation' => 'NewSecurePass456',
            ]);

        $response->assertStatus(200);
        $this->user->refresh();
        $this->assertTrue(Hash::check('NewSecurePass456', $this->user->password));
    }

    public function test_change_password_rejects_wrong_current(): void
    {
        $this->user->update(['password' => Hash::make('CorrectPassword')]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/v1/profile/change-password', [
                'current_password' => 'WrongPassword',
                'new_password' => 'NewPassword123',
                'new_password_confirmation' => 'NewPassword123',
            ]);

        $response->assertStatus(422);
    }

    public function test_unauthenticated_cannot_view_profile(): void
    {
        $response = $this->getJson('/api/v1/profile');
        $response->assertStatus(401);
    }
}
