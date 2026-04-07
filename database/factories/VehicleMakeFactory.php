<?php

namespace Database\Factories;

use App\Models\VehicleMake;
use Illuminate\Database\Eloquent\Factories\Factory;

class VehicleMakeFactory extends Factory
{
    protected $model = VehicleMake::class;

    public function definition(): array
    {
        $name = fake()->unique()->randomElement([
            'BMW', 'Audi', 'Mercedes-Benz', 'Volkswagen', 'Toyota',
            'Honda', 'Ford', 'Opel', 'Peugeot', 'Renault',
            'Skoda', 'Hyundai', 'Kia', 'Fiat', 'Nissan',
            'Mazda', 'Volvo', 'Porsche', 'Citroen', 'SEAT',
        ]);

        return [
            'name' => $name,
            'slug' => \Str::slug($name),
            'logo' => null,
            'type' => fake()->randomElement(['car', 'motorcycle', 'truck', 'caravan']),
        ];
    }
}
