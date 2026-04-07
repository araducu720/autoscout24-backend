<?php

namespace Database\Factories;

use App\Models\NotificationPreference;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationPreferenceFactory extends Factory
{
    protected $model = NotificationPreference::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'channel' => fake()->randomElement(['email', 'push', 'sms']),
            'payment_received' => true,
            'payment_verified' => true,
            'transaction_update' => true,
            'message_received' => true,
            'dispute_update' => true,
            'price_alert' => true,
            'new_listing_match' => true,
            'pickup_reminder' => true,
            'marketing' => false,
            'weekly_digest' => true,
        ];
    }

    public function allDisabled(): self
    {
        return $this->state(fn () => [
            'payment_received' => false,
            'payment_verified' => false,
            'transaction_update' => false,
            'message_received' => false,
            'dispute_update' => false,
            'price_alert' => false,
            'new_listing_match' => false,
            'pickup_reminder' => false,
            'marketing' => false,
            'weekly_digest' => false,
        ]);
    }
}
