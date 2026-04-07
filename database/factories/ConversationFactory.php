<?php

namespace Database\Factories;

use App\Models\Conversation;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConversationFactory extends Factory
{
    protected $model = Conversation::class;

    public function definition(): array
    {
        return [
            'buyer_id' => User::factory(),
            'seller_id' => User::factory(),
            'vehicle_id' => Vehicle::factory(),
            'last_message' => fake()->sentence(),
            'last_message_at' => now()->subMinutes(fake()->numberBetween(1, 10080)),
        ];
    }
}
