<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vehicle;
use App\Models\VehicleMake;
use App\Models\VehicleModel;
use App\Models\VehicleImage;

class VehicleSeeder extends Seeder
{
    public function run(): void
    {
        $audi = VehicleMake::where('name', 'Audi')->first();
        $audiA4 = VehicleModel::where('name', 'A4')->where('make_id', $audi->id)->first();
        
        $bmw = VehicleMake::where('name', 'BMW')->first();
        $bmw3Series = VehicleModel::where('name', 'Series 3')->where('make_id', $bmw->id)->first();

        $harley = VehicleMake::where('name', 'Harley-Davidson')->first();
        $harleyStreet = VehicleModel::where('name', 'Street 750')->where('make_id', $harley->id)->first();

        $scaniaTruck = VehicleMake::where('name', 'Scania')->where('type', 'truck')->first();
        $scaniaR450 = VehicleModel::where('name', 'R450')->where('make_id', $scaniaTruck?->id)->first();

        $hymer = VehicleMake::where('name', 'Hymer')->where('type', 'caravan')->first();
        $hymerClass = VehicleModel::where('name', 'Class B')->where('make_id', $hymer?->id)->first();

        $vehicles = [
            // CARS
            [
                'make_id' => $audi->id,
                'model_id' => $audiA4?->id,
                'title' => 'Audi A4 2.0 TDI - Excellent Condition',
                'description' => 'Well maintained Audi A4 with full service history. Leather interior, navigation system, parking sensors.',
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'price' => 25900,
                'year' => 2020,
                'mileage' => 45000,
                'fuel_type' => 'diesel',
                'transmission' => 'automatic',
                'body_type' => 'sedan',
                'color' => 'Black',
                'doors' => 4,
                'seats' => 5,
                'engine_size' => 2000,
                'power' => 190,
                'country' => 'Germany',
                'city' => 'Berlin',
                'condition' => 'used',
                'status' => 'active',
                'is_featured' => true,
                'features' => [
                    'air_conditioning',
                    'navigation',
                    'parking_sensors',
                    'bluetooth',
                    'alloy_wheels',
                ],
            ],
            [
                'make_id' => $bmw->id,
                'model_id' => $bmw3Series?->id,
                'title' => 'BMW 320d M Sport - Low Mileage',
                'description' => 'Stunning BMW 320d with M Sport package. One owner, full BMW service history.',
                'video_url' => 'https://www.youtube.com/watch?v=jNQXAC9IVRw',
                'price' => 32500,
                'year' => 2021,
                'mileage' => 28000,
                'fuel_type' => 'diesel',
                'transmission' => 'automatic',
                'body_type' => 'sedan',
                'color' => 'White',
                'doors' => 4,
                'seats' => 5,
                'engine_size' => 2000,
                'power' => 200,
                'country' => 'Germany',
                'city' => 'Munich',
                'condition' => 'used',
                'status' => 'active',
                'is_featured' => true,
                'features' => [
                    'leather_seats',
                    'navigation',
                    'heated_seats',
                    'parking_camera',
                ],
            ],
            // MOTORCYCLES
            [
                'make_id' => $harley->id,
                'model_id' => $harleyStreet?->id,
                'title' => 'Harley-Davidson Street 750 - Custom Paint',
                'description' => 'Classic American motorcycle. Recently serviced with new tyres.',
                'video_url' => 'https://www.youtube.com/watch?v=9bZkp7q19f0',
                'price' => 8500,
                'year' => 2019,
                'mileage' => 12500,
                'fuel_type' => 'petrol',
                'transmission' => 'manual',
                'body_type' => 'cruiser',
                'color' => 'Red',
                'doors' => 0,
                'seats' => 2,
                'engine_size' => 750,
                'power' => 53,
                'country' => 'USA',
                'city' => 'Chicago',
                'condition' => 'used',
                'status' => 'active',
                'is_featured' => false,
                'features' => [
                    'abs',
                    'traction_control',
                    'led_lights',
                    'windshield',
                ],
            ],
        ];

        // Add truck if exists
        if ($scaniaTruck) {
            $vehicles[] = [
                'make_id' => $scaniaTruck->id,
                'model_id' => $scaniaR450?->id,
                'title' => 'Scania R450 Euro 6 - Long Haul Truck',
                'description' => 'Professional long-haul truck. Excellent condition, well maintained.',
                'video_url' => 'https://www.youtube.com/watch?v=ub82Xb1C8os',
                'price' => 85000,
                'year' => 2018,
                'mileage' => 450000,
                'fuel_type' => 'diesel',
                'transmission' => 'automatic',
                'body_type' => 'truck',
                'color' => 'Silver',
                'doors' => 2,
                'seats' => 2,
                'engine_size' => 16000,
                'power' => 540,
                'country' => 'Sweden',
                'city' => 'Gothenburg',
                'condition' => 'used',
                'status' => 'active',
                'is_featured' => false,
                'features' => [
                    'retarder',
                    'cruise_control',
                    'sleeping_cab',
                ],
            ];
        }

        // Add caravan if exists
        if ($hymer) {
            $vehicles[] = [
                'make_id' => $hymer->id,
                'model_id' => $hymerClass?->id,
                'title' => 'Hymer Class B - Modern Motorhome',
                'description' => 'Luxury motorhome with full amenities. Perfect for family holidays.',
                'video_url' => 'https://www.youtube.com/watch?v=hhDU74simpQ',
                'price' => 95000,
                'year' => 2022,
                'mileage' => 8500,
                'fuel_type' => 'diesel',
                'transmission' => 'automatic',
                'body_type' => 'motorhome',
                'color' => 'White',
                'doors' => 2,
                'seats' => 4,
                'engine_size' => 2200,
                'power' => 163,
                'country' => 'Germany',
                'city' => 'Bad Waldsee',
                'condition' => 'used',
                'status' => 'active',
                'is_featured' => false,
                'features' => [
                    'air_conditioning',
                    'solar_panels',
                    'tv',
                    'kitchen',
                    'shower',
                ],
            ];
        }

        foreach ($vehicles as $vehicleData) {
            $vehicle = Vehicle::create($vehicleData);
            
            // Add placeholder image
            VehicleImage::create([
                'vehicle_id' => $vehicle->id,
                'image_path' => 'placeholder/vehicle-' . $vehicle->id . '.jpg',
                'is_primary' => true,
                'order' => 1,
            ]);
        }
    }
}
