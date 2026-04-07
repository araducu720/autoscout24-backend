<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\VehicleMake;
use App\Models\VehicleModel;
use Illuminate\Database\Eloquent\Factories\Factory;

class VehicleFactory extends Factory
{
    public function definition(): array
    {
        // Get or create a make
        $make = VehicleMake::inRandomOrder()->first();
        if (!$make) {
            $makeName = fake()->randomElement(['BMW', 'Audi', 'Mercedes', 'Volkswagen', 'Toyota']);
            $make = VehicleMake::create([
                'name' => $makeName,
                'slug' => \Str::slug($makeName),
                'type' => 'car',
            ]);
        }

        // Get or create a model for this make
        $model = VehicleModel::where('make_id', $make->id)->inRandomOrder()->first();
        if (!$model) {
            $modelName = fake()->randomElement(['Series 3', 'A4', 'C-Class', 'Golf', 'Camry']);
            $model = VehicleModel::create([
                'make_id' => $make->id,
                'name' => $modelName,
                'slug' => \Str::slug($modelName),
            ]);
        }
        
        $year = fake()->numberBetween(2015, 2024);
        $condition = fake()->randomElement(['new', 'used']);

        return [
            'user_id' => User::factory(),
            'make_id' => $make->id,
            'model_id' => $model->id,
            'title' => "{$make->name} {$model->name} {$year}",
            'description' => fake()->paragraph(3),
            'price' => fake()->numberBetween(5000, 50000),
            'year' => $year,
            'mileage' => $condition === 'new' ? 0 : fake()->numberBetween(10000, 150000),
            'fuel_type' => fake()->randomElement(['petrol', 'diesel', 'electric', 'hybrid', 'lpg']),
            'transmission' => fake()->randomElement(['manual', 'automatic']),
            'body_type' => fake()->randomElement(['sedan', 'suv', 'coupe', 'hatchback', 'wagon', 'convertible', 'van', 'pickup']),
            'engine_size' => fake()->numberBetween(1000, 5000), // in cc
            'power' => fake()->numberBetween(80, 400),
            'doors' => fake()->randomElement([2, 3, 4, 5]),
            'seats' => fake()->randomElement([2, 4, 5, 7]),
            'color' => fake()->safeColorName(),
            'condition' => $condition,
            'city' => fake()->city(),
            'country' => fake()->country(),
            'status' => 'active',
            'is_featured' => fake()->boolean(20), // 20% chance
            'views_count' => fake()->numberBetween(0, 1000),
        ];
    }
}
