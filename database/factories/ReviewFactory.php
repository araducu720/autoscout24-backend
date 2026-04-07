<?php

namespace Database\Factories;

use App\Models\Review;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    protected $model = Review::class;

    public function definition(): array
    {
        return [
            'vehicle_id' => Vehicle::factory(),
            'user_id' => User::factory(),
            'transaction_id' => null,
            'rating' => fake()->numberBetween(1, 5),
            'comment' => fake()->paragraph(),
            'rating_vehicle' => fake()->numberBetween(1, 5),
            'rating_seller' => fake()->numberBetween(1, 5),
            'rating_shipping' => fake()->optional()->numberBetween(1, 5),
            'photos' => null,
            'anonymous' => fake()->boolean(20),
            'helpful_count' => fake()->numberBetween(0, 50),
            'status' => fake()->randomElement(['pending', 'approved', 'rejected']),
        ];
    }

    public function approved(): self
    {
        return $this->state(fn () => ['status' => 'approved']);
    }

    public function withPhotos(): self
    {
        return $this->state(fn () => [
            'photos' => [
                'reviews/' . fake()->uuid() . '.jpg',
                'reviews/' . fake()->uuid() . '.jpg',
            ],
        ]);
    }
}
