<?php

namespace Database\Factories;

use App\Models\VehicleImage;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

class VehicleImageFactory extends Factory
{
    protected $model = VehicleImage::class;

    public function definition(): array
    {
        return [
            'vehicle_id' => Vehicle::factory(),
            'image_path' => 'vehicles/' . fake()->numberBetween(1, 999) . '/' . fake()->uuid() . '.jpg',
            'is_primary' => false,
            'order' => fake()->numberBetween(0, 10),
        ];
    }

    public function primary(): self
    {
        return $this->state(fn () => ['is_primary' => true, 'order' => 0]);
    }
}
