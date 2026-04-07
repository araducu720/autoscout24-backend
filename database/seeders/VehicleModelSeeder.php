<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VehicleMake;
use App\Models\VehicleModel;
use Illuminate\Support\Str;

class VehicleModelSeeder extends Seeder
{
    public function run(): void
    {
        $modelsData = [
            'Audi' => ['A3', 'A4', 'A6', 'Q3', 'Q5', 'Q7', 'TT'],
            'BMW' => ['Series 1', 'Series 3', 'Series 5', 'X1', 'X3', 'X5'],
            'Mercedes-Benz' => ['A-Class', 'C-Class', 'E-Class', 'GLA', 'GLC', 'GLE'],
            'Volkswagen' => ['Golf', 'Passat', 'Tiguan', 'Polo', 'Arteon'],
            'Toyota' => ['Corolla', 'Camry', 'RAV4', 'Yaris', 'Hilux'],
            'Honda' => ['Civic', 'Accord', 'CR-V', 'HR-V'],
        ];

        // Car models
        foreach ($modelsData as $makeName => $models) {
            $make = VehicleMake::where('name', $makeName)->where('type', 'car')->first();
            
            if ($make) {
                foreach ($models as $modelName) {
                    VehicleModel::create([
                        'make_id' => $make->id,
                        'name' => $modelName,
                        'slug' => Str::slug($modelName),
                    ]);
                }
            }
        }

        // Motorcycle models
        $motorcycleMakeModels = [
            'Harley-Davidson' => ['Street 750', 'Street 500', 'Touring'],
            'Yamaha' => ['YZF-R1', 'MT-09', 'Tracer'],
            'Kawasaki' => ['Ninja H2', 'Z900', 'Versys'],
            'Suzuki' => ['GSX-R1000', 'SV650', 'Bandit'],
        ];

        foreach ($motorcycleMakeModels as $makeName => $models) {
            $make = VehicleMake::where('name', $makeName)->where('type', 'motorcycle')->first();
            
            if ($make) {
                foreach ($models as $modelName) {
                    VehicleModel::create([
                        'make_id' => $make->id,
                        'name' => $modelName,
                        'slug' => Str::slug($modelName),
                    ]);
                }
            }
        }

        // Truck models
        $truckMakeModels = [
            'Scania' => ['R450', 'R500', 'R560'],
            'DAF' => ['XF460', 'XF510', 'FTG'],
            'MAN' => ['TGX 18.440', 'TGX 18.540'],
        ];

        foreach ($truckMakeModels as $makeName => $models) {
            $make = VehicleMake::where('name', $makeName)->where('type', 'truck')->first();
            
            if ($make) {
                foreach ($models as $modelName) {
                    VehicleModel::create([
                        'make_id' => $make->id,
                        'name' => $modelName,
                        'slug' => Str::slug($modelName),
                    ]);
                }
            }
        }

        // Caravan models
        $caravanMakeModels = [
            'Hymer' => ['Class B', 'Class S', 'Van'],
            'Adria' => ['Action', 'Compact'],
            'Hobby' => ['A-class', 'Premium'],
        ];

        foreach ($caravanMakeModels as $makeName => $models) {
            $make = VehicleMake::where('name', $makeName)->where('type', 'caravan')->first();
            
            if ($make) {
                foreach ($models as $modelName) {
                    VehicleModel::create([
                        'make_id' => $make->id,
                        'name' => $modelName,
                        'slug' => Str::slug($modelName),
                    ]);
                }
            }
        }
    }
}
