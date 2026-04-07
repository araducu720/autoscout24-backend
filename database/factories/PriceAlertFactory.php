<?php

namespace Database\Factories;

use App\Models\PriceAlert;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

class PriceAlertFactory extends Factory
{
    protected $model = PriceAlert::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'vehicle_id' => Vehicle::factory(),
            'target_price' => fake()->numberBetween(5000, 40000),
            'current_price' => fake()->numberBetween(5000, 50000),
            'alert_type' => fake()->randomElement(['below', 'above', 'change', 'drop_percent']),
            'is_active' => true,
            'triggered_count' => 0,
            'notify_email' => true,
            'notify_push' => false,
        ];
    }

    public function inactive(): self
    {
        return $this->state(fn () => ['is_active' => false]);
    }

    public function triggered(): self
    {
        return $this->state(fn () => [
            'triggered_count' => fake()->numberBetween(1, 10),
            'last_triggered_at' => now()->subDays(fake()->numberBetween(1, 30)),
        ]);
    }
}
