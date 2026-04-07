<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VehicleMake;
use Illuminate\Support\Str;

class VehicleMakeSeeder extends Seeder
{
    public function run(): void
    {
        $carMakes = [
            'Audi', 'BMW', 'Mercedes-Benz', 'Volkswagen', 'Ford', 'Toyota', 
            'Honda', 'Nissan', 'Hyundai', 'Kia', 'Mazda', 'Volvo', 
            'Peugeot', 'Renault', 'Fiat', 'Opel', 'Skoda', 'Seat'
        ];

        $motorcycleMakes = [
            'Harley-Davidson', 'Yamaha', 'Kawasaki', 'Suzuki', 
            'Ducati', 'KTM', 'Triumph', 'Aprilia'
        ];

        $truckMakes = [
            'Scania', 'DAF', 'Iveco', 'MAN',
            'Mack', 'Peterbilt', 'Freightliner', 'Actros', 'Tatra', 'KAMAZ'
        ];

        $caravanMakes = [
            'Hymer', 'Mobilvetta', 'Rimor', 'Adria', 'Eura Mobil',
            'Dethleffs', 'Carado', 'Hobby', 'LMC', 'Sunlight'
        ];

        foreach ($carMakes as $make) {
            VehicleMake::create([
                'name' => $make,
                'slug' => Str::slug($make),
                'type' => 'car',
            ]);
        }

        foreach ($motorcycleMakes as $make) {
            VehicleMake::create([
                'name' => $make,
                'slug' => Str::slug($make),
                'type' => 'motorcycle',
            ]);
        }

        foreach ($truckMakes as $make) {
            VehicleMake::create([
                'name' => $make,
                'slug' => Str::slug($make),
                'type' => 'truck',
            ]);
        }

        foreach ($caravanMakes as $make) {
            VehicleMake::create([
                'name' => $make,
                'slug' => Str::slug($make),
                'type' => 'caravan',
            ]);
        }
    }
}
