<?php

namespace Database\Factories;

use App\Models\PhoneReveal;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

class PhoneRevealFactory extends Factory
{
    protected $model = PhoneReveal::class;

    public function definition(): array
    {
        return [
            'vehicle_id' => Vehicle::factory(),
            'user_id' => User::factory(),
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
        ];
    }
}
