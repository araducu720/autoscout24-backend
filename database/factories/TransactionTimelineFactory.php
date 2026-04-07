<?php

namespace Database\Factories;

use App\Models\TransactionTimeline;
use App\Models\SafetradeTransaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionTimelineFactory extends Factory
{
    protected $model = TransactionTimeline::class;

    public function definition(): array
    {
        return [
            'safetrade_transaction_id' => SafetradeTransaction::factory(),
            'event' => fake()->randomElement(['created', 'confirmed', 'payment_received', 'in_transit', 'delivered', 'completed']),
            'description' => fake()->sentence(),
            'actor_id' => User::factory(),
            'actor_name' => fake()->name(),
            'actor_role' => fake()->randomElement(['buyer', 'seller', 'admin', 'system']),
            'metadata' => null,
            'timestamp' => now()->subMinutes(fake()->numberBetween(1, 10080)),
        ];
    }
}
