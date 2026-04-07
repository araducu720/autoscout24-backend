<?php

namespace Database\Factories;

use App\Models\TestDriveRequest;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

class TestDriveRequestFactory extends Factory
{
    protected $model = TestDriveRequest::class;

    public function definition(): array
    {
        return [
            'vehicle_id' => Vehicle::factory(),
            'user_id' => User::factory(),
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'preferred_date' => now()->addDays(fake()->numberBetween(1, 14)),
            'preferred_time' => fake()->time('H:i'),
            'message' => fake()->optional()->sentence(),
            'status' => fake()->randomElement(['pending', 'confirmed', 'completed', 'cancelled']),
        ];
    }

    public function confirmed(): self
    {
        return $this->state(fn () => ['status' => 'confirmed']);
    }
}
