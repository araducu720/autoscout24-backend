<?php

namespace Database\Factories;

use App\Models\VehicleModel;
use App\Models\VehicleMake;
use Illuminate\Database\Eloquent\Factories\Factory;

class VehicleModelFactory extends Factory
{
    protected $model = VehicleModel::class;

    public function definition(): array
    {
        $name = fake()->randomElement([
            'Series 3', 'A4', 'C-Class', 'Golf', 'Camry',
            'Civic', 'Focus', 'Astra', '308', 'Clio',
            'Octavia', 'Tucson', 'Sportage', '500', 'Qashqai',
        ]);

        return [
            'make_id' => VehicleMake::factory(),
            'name' => $name,
            'slug' => \Str::slug($name),
        ];
    }
}
