<?php

namespace Database\Factories;

use App\Models\SavedSearch;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SavedSearchFactory extends Factory
{
    protected $model = SavedSearch::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->sentence(3),
            'filters' => [
                'make_id' => fake()->numberBetween(1, 50),
                'price_min' => fake()->numberBetween(1000, 10000),
                'price_max' => fake()->numberBetween(10000, 50000),
                'fuel_type' => fake()->randomElement(['petrol', 'diesel', 'electric']),
            ],
            'notify_new_matches' => true,
            'last_checked_at' => now()->subHours(fake()->numberBetween(1, 72)),
        ];
    }
}
