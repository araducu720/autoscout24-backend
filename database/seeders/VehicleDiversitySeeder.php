<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vehicle;
use App\Models\VehicleMake;
use App\Models\VehicleModel;
use App\Models\VehicleImage;
use Illuminate\Support\Str;

/**
 * Seeds diverse vehicles across all categories: cars, motorcycles, trucks, caravans.
 * Run this after VehicleMakeSeeder and VehicleModelSeeder.
 */
class VehicleDiversitySeeder extends Seeder
{
    private array $carCities = [
        ['DE', 'Berlin'], ['DE', 'Munich'], ['DE', 'Hamburg'], ['DE', 'Frankfurt'],
        ['NL', 'Amsterdam'], ['NL', 'Rotterdam'], ['NL', 'Den Haag'],
        ['IT', 'Milan'], ['IT', 'Rome'], ['IT', 'Turin'],
        ['AT', 'Vienna'], ['AT', 'Salzburg'],
        ['ES', 'Madrid'], ['ES', 'Barcelona'],
        ['FR', 'Paris'], ['FR', 'Lyon'],
        ['BE', 'Brussels'], ['BE', 'Antwerp'],
    ];

    private array $carColors = ['Black', 'White', 'Silver', 'Grey', 'Blue', 'Red', 'Green', 'Brown', 'Orange', 'Beige'];

    public function run(): void
    {
        $this->seedCars();
        $this->seedMotorcycles();
        $this->seedTrucks();
        $this->seedCaravans();
    }

    private function seedCars(): void
    {
        $carSpecs = [
            // BMW models
            ['BMW', 'Series 3', 'BMW 320d xDrive M Sport', 'sedan', 'diesel', 28000, 55000, 2021, 'automatic', 190, 2000, 4, 5],
            ['BMW', 'Series 3', 'BMW 330e Hybrid Sedan', 'sedan', 'hybrid', 42000, 32000, 2022, 'automatic', 292, 2000, 4, 5],
            ['BMW', 'Series 5', 'BMW 530d Touring Business', 'wagon', 'diesel', 45000, 68000, 2020, 'automatic', 286, 3000, 4, 5],
            ['BMW', 'X3', 'BMW X3 xDrive30d M Sport', 'suv', 'diesel', 52000, 45000, 2021, 'automatic', 265, 3000, 4, 5],
            ['BMW', 'X5', 'BMW X5 45e xDrive Hybrid', 'suv', 'hybrid', 75000, 28000, 2022, 'automatic', 394, 3000, 4, 7],
            ['BMW', 'X1', 'BMW X1 sDrive18i', 'suv', 'petrol', 32000, 15000, 2023, 'automatic', 136, 1500, 4, 5],
            ['BMW', 'Series 1', 'BMW 118i M Sport', 'hatchback', 'petrol', 28000, 22000, 2022, 'automatic', 140, 1500, 4, 5],
            ['BMW', 'Series 3', 'BMW M340i xDrive', 'sedan', 'petrol', 58000, 18000, 2023, 'automatic', 374, 3000, 4, 5],

            // Mercedes models
            ['Mercedes-Benz', 'C-Class', 'Mercedes C220d AMG Line', 'sedan', 'diesel', 38000, 42000, 2021, 'automatic', 200, 2000, 4, 5],
            ['Mercedes-Benz', 'E-Class', 'Mercedes E300e Plug-in Hybrid', 'sedan', 'hybrid', 52000, 35000, 2022, 'automatic', 320, 2000, 4, 5],
            ['Mercedes-Benz', 'GLC', 'Mercedes GLC 300 4MATIC', 'suv', 'petrol', 55000, 25000, 2022, 'automatic', 258, 2000, 4, 5],
            ['Mercedes-Benz', 'GLE', 'Mercedes GLE 350d 4MATIC', 'suv', 'diesel', 72000, 38000, 2021, 'automatic', 272, 3000, 4, 7],
            ['Mercedes-Benz', 'A-Class', 'Mercedes A180 Progressive', 'hatchback', 'petrol', 26000, 18000, 2023, 'automatic', 136, 1400, 4, 5],
            ['Mercedes-Benz', 'GLA', 'Mercedes GLA 250e Hybrid', 'suv', 'hybrid', 42000, 22000, 2023, 'automatic', 218, 1400, 4, 5],

            // Volkswagen models
            ['Volkswagen', 'Golf', 'VW Golf 8 GTI', 'hatchback', 'petrol', 38000, 15000, 2023, 'automatic', 245, 2000, 4, 5],
            ['Volkswagen', 'Golf', 'VW Golf 8 GTE Hybrid', 'hatchback', 'hybrid', 35000, 20000, 2022, 'automatic', 245, 1400, 4, 5],
            ['Volkswagen', 'Passat', 'VW Passat Variant Business', 'wagon', 'diesel', 32000, 55000, 2021, 'automatic', 200, 2000, 4, 5],
            ['Volkswagen', 'Tiguan', 'VW Tiguan 2.0 TDI R-Line', 'suv', 'diesel', 42000, 35000, 2022, 'automatic', 200, 2000, 4, 5],
            ['Volkswagen', 'Polo', 'VW Polo 1.0 TSI Style', 'hatchback', 'petrol', 18000, 8000, 2023, 'automatic', 110, 1000, 4, 5],
            ['Volkswagen', 'Arteon', 'VW Arteon Shooting Brake R', 'wagon', 'petrol', 52000, 22000, 2022, 'automatic', 320, 2000, 4, 5],

            // Ford models
            ['Ford', null, 'Ford Focus ST 2.3 EcoBoost', 'hatchback', 'petrol', 32000, 18000, 2022, 'manual', 280, 2300, 4, 5],
            ['Ford', null, 'Ford Puma 1.0 EcoBoost Hybrid', 'suv', 'hybrid', 25000, 12000, 2023, 'automatic', 155, 1000, 4, 5],
            ['Ford', null, 'Ford Mustang Mach-E AWD Extended', 'suv', 'electric', 58000, 15000, 2023, 'automatic', 351, 0, 4, 5],
            ['Ford', null, 'Ford Kuga 2.5 Plug-in Hybrid', 'suv', 'hybrid', 38000, 25000, 2022, 'automatic', 225, 2500, 4, 5],

            // Toyota models
            ['Toyota', 'Corolla', 'Toyota Corolla 2.0 Hybrid', 'hatchback', 'hybrid', 28000, 20000, 2023, 'automatic', 196, 2000, 4, 5],
            ['Toyota', 'RAV4', 'Toyota RAV4 2.5 Hybrid AWD', 'suv', 'hybrid', 42000, 15000, 2023, 'automatic', 222, 2500, 4, 5],
            ['Toyota', 'Yaris', 'Toyota Yaris 1.5 Hybrid', 'hatchback', 'hybrid', 22000, 8000, 2023, 'automatic', 116, 1500, 4, 5],
            ['Toyota', 'Camry', 'Toyota Camry 2.5 Hybrid Executive', 'sedan', 'hybrid', 38000, 30000, 2022, 'automatic', 218, 2500, 4, 5],

            // Honda models  
            ['Honda', 'Civic', 'Honda Civic 2.0 e:HEV Sport', 'hatchback', 'hybrid', 35000, 10000, 2023, 'automatic', 184, 2000, 4, 5],
            ['Honda', 'CR-V', 'Honda CR-V 2.0 e:HEV AWD', 'suv', 'hybrid', 45000, 18000, 2023, 'automatic', 204, 2000, 4, 5],
            ['Honda', 'HR-V', 'Honda HR-V 1.5 e:HEV Elegance', 'suv', 'hybrid', 32000, 8000, 2023, 'automatic', 131, 1500, 4, 5],

            // Hyundai & Kia
            ['Hyundai', null, 'Hyundai Tucson 1.6 T-GDI HEV', 'suv', 'hybrid', 35000, 15000, 2023, 'automatic', 230, 1600, 4, 5],
            ['Hyundai', null, 'Hyundai Ioniq 5 Long Range AWD', 'suv', 'electric', 48000, 12000, 2023, 'automatic', 325, 0, 4, 5],
            ['Kia', null, 'Kia EV6 GT-Line Long Range', 'suv', 'electric', 52000, 10000, 2023, 'automatic', 325, 0, 4, 5],
            ['Kia', null, 'Kia Sportage 1.6 T-GDI HEV', 'suv', 'hybrid', 38000, 18000, 2023, 'automatic', 230, 1600, 4, 5],

            // Others
            ['Renault', null, 'Renault Megane E-Tech Electric', 'hatchback', 'electric', 38000, 8000, 2023, 'automatic', 220, 0, 4, 5],
            ['Peugeot', null, 'Peugeot e-208 Electric', 'hatchback', 'electric', 32000, 15000, 2023, 'automatic', 136, 0, 4, 5],
            ['Peugeot', null, 'Peugeot 3008 Hybrid4 AWD', 'suv', 'hybrid', 48000, 25000, 2022, 'automatic', 300, 1600, 4, 5],
            ['Opel', null, 'Opel Corsa-e Electric', 'hatchback', 'electric', 30000, 12000, 2023, 'automatic', 136, 0, 4, 5],
            ['Opel', null, 'Opel Mokka-e Electric', 'suv', 'electric', 35000, 8000, 2023, 'automatic', 136, 0, 4, 5],
            ['Skoda', null, 'Skoda Octavia Combi 2.0 TDI', 'wagon', 'diesel', 32000, 40000, 2022, 'automatic', 150, 2000, 4, 5],
            ['Skoda', null, 'Skoda Enyaq iV 80', 'suv', 'electric', 42000, 10000, 2023, 'automatic', 204, 0, 4, 5],
            ['Seat', null, 'CUPRA Formentor 2.0 TSI 4Drive', 'suv', 'petrol', 42000, 20000, 2022, 'automatic', 310, 2000, 4, 5],
            ['Volvo', null, 'Volvo XC40 Recharge Pure Electric', 'suv', 'electric', 50000, 12000, 2023, 'automatic', 408, 0, 4, 5],
            ['Volvo', null, 'Volvo XC60 T8 Recharge', 'suv', 'hybrid', 62000, 25000, 2022, 'automatic', 455, 2000, 4, 5],
            ['Fiat', null, 'Fiat 500 Electric Action', 'hatchback', 'electric', 26000, 5000, 2023, 'automatic', 95, 0, 2, 4],
            ['Mazda', null, 'Mazda CX-5 2.5 Skyactiv-G AWD', 'suv', 'petrol', 38000, 22000, 2022, 'automatic', 194, 2500, 4, 5],
            ['Nissan', null, 'Nissan Qashqai e-Power', 'suv', 'hybrid', 35000, 10000, 2023, 'automatic', 190, 1500, 4, 5],
            ['Nissan', null, 'Nissan Leaf e+ 62kWh', 'hatchback', 'electric', 32000, 18000, 2022, 'automatic', 217, 0, 4, 5],

            // Budget / Used cars
            ['Volkswagen', 'Golf', 'VW Golf 7 1.6 TDI Comfortline', 'hatchback', 'diesel', 12000, 95000, 2017, 'manual', 115, 1600, 4, 5],
            ['Ford', null, 'Ford Fiesta 1.0 EcoBoost Trend', 'hatchback', 'petrol', 8500, 65000, 2018, 'manual', 100, 1000, 4, 5],
            ['Renault', null, 'Renault Clio 1.5 dCi Zen', 'hatchback', 'diesel', 9000, 78000, 2019, 'manual', 85, 1500, 4, 5],
            ['Opel', null, 'Opel Astra 1.6 CDTI Dynamic', 'hatchback', 'diesel', 11000, 72000, 2018, 'manual', 136, 1600, 4, 5],
            ['Volkswagen', 'Polo', 'VW Polo 1.2 TSI Comfortline', 'hatchback', 'petrol', 7500, 88000, 2016, 'manual', 90, 1200, 4, 5],
            ['Peugeot', null, 'Peugeot 308 1.6 HDI Active', 'hatchback', 'diesel', 8000, 105000, 2017, 'manual', 120, 1600, 4, 5],
            ['Skoda', null, 'Skoda Fabia 1.0 TSI Ambition', 'hatchback', 'petrol', 6500, 92000, 2016, 'manual', 95, 1000, 4, 5],
            ['Toyota', 'Yaris', 'Toyota Yaris 1.3 Luna', 'hatchback', 'petrol', 5500, 110000, 2015, 'manual', 99, 1300, 4, 5],

            // Convertibles / Coupes
            ['BMW', 'Series 3', 'BMW M4 Competition Cabrio', 'convertible', 'petrol', 92000, 12000, 2023, 'automatic', 510, 3000, 2, 4],
            ['Mercedes-Benz', 'C-Class', 'Mercedes C200 Cabrio AMG', 'convertible', 'petrol', 52000, 28000, 2021, 'automatic', 204, 2000, 2, 4],
            ['Audi', 'TT', 'Audi TT RS Coupe', 'coupe', 'petrol', 68000, 20000, 2022, 'automatic', 400, 2500, 2, 4],
            ['BMW', 'Series 3', 'BMW M2 Competition', 'coupe', 'petrol', 55000, 25000, 2022, 'automatic', 410, 3000, 2, 4],
        ];

        foreach ($carSpecs as $spec) {
            $this->createVehicle($spec[0], $spec[1], $spec[2], $spec[3], $spec[4], $spec[5], $spec[6], $spec[7], $spec[8], $spec[9], $spec[10], $spec[11], $spec[12]);
        }
    }

    private function seedMotorcycles(): void
    {
        $motorcycleSpecs = [
            // Harley-Davidson
            ['Harley-Davidson', 'Touring', 'Harley-Davidson Road Glide Special', 'cruiser', 'petrol', 32000, 8000, 2022, 'manual', 94, 1868, 0, 2],
            ['Harley-Davidson', 'Street 750', 'Harley-Davidson Iron 883', 'cruiser', 'petrol', 9500, 15000, 2020, 'manual', 50, 883, 0, 2],
            ['Harley-Davidson', 'Touring', 'Harley-Davidson Fat Boy 114', 'cruiser', 'petrol', 24000, 5000, 2023, 'manual', 93, 1868, 0, 2],
            ['Harley-Davidson', 'Street 500', 'Harley-Davidson Sportster S', 'cruiser', 'petrol', 16500, 3000, 2023, 'manual', 121, 1252, 0, 2],

            // Yamaha
            ['Yamaha', 'MT-09', 'Yamaha MT-09 SP', 'motorcycle', 'petrol', 11500, 6000, 2023, 'manual', 119, 890, 0, 2],
            ['Yamaha', 'YZF-R1', 'Yamaha YZF-R1M', 'motorcycle', 'petrol', 24000, 3000, 2023, 'manual', 200, 998, 0, 2],
            ['Yamaha', 'Tracer', 'Yamaha Tracer 9 GT+', 'motorcycle', 'petrol', 14500, 8000, 2023, 'manual', 119, 890, 0, 2],
            ['Yamaha', 'MT-09', 'Yamaha MT-07', 'motorcycle', 'petrol', 7500, 12000, 2022, 'manual', 73, 689, 0, 2],

            // Kawasaki
            ['Kawasaki', 'Ninja H2', 'Kawasaki Ninja ZX-10R', 'motorcycle', 'petrol', 18000, 4000, 2023, 'manual', 203, 998, 0, 2],
            ['Kawasaki', 'Z900', 'Kawasaki Z900 Performance', 'motorcycle', 'petrol', 10500, 8000, 2023, 'manual', 125, 948, 0, 2],
            ['Kawasaki', 'Versys', 'Kawasaki Versys 1000 S', 'motorcycle', 'petrol', 14000, 10000, 2022, 'manual', 120, 1043, 0, 2],
            ['Kawasaki', 'Z900', 'Kawasaki Z650', 'motorcycle', 'petrol', 7200, 15000, 2022, 'manual', 68, 649, 0, 2],

            // Suzuki
            ['Suzuki', 'GSX-R1000', 'Suzuki GSX-R1000R', 'motorcycle', 'petrol', 19000, 3000, 2023, 'manual', 199, 999, 0, 2],
            ['Suzuki', 'SV650', 'Suzuki SV650 ABS', 'motorcycle', 'petrol', 6800, 12000, 2022, 'manual', 76, 645, 0, 2],
            ['Suzuki', 'Bandit', 'Suzuki V-Strom 1050 XT', 'motorcycle', 'petrol', 13500, 8000, 2023, 'manual', 107, 1037, 0, 2],

            // Ducati
            ['Ducati', null, 'Ducati Panigale V4S', 'motorcycle', 'petrol', 32000, 2000, 2023, 'manual', 214, 1103, 0, 2],
            ['Ducati', null, 'Ducati Monster 937', 'motorcycle', 'petrol', 12500, 5000, 2023, 'manual', 111, 937, 0, 2],
            ['Ducati', null, 'Ducati Multistrada V4 S', 'motorcycle', 'petrol', 24000, 8000, 2023, 'manual', 170, 1158, 0, 2],
            ['Ducati', null, 'Ducati Scrambler 800', 'motorcycle', 'petrol', 9500, 10000, 2022, 'manual', 73, 803, 0, 2],

            // KTM
            ['KTM', null, 'KTM 1290 Super Duke R', 'motorcycle', 'petrol', 20000, 4000, 2023, 'manual', 180, 1301, 0, 2],
            ['KTM', null, 'KTM 890 Adventure R', 'motorcycle', 'petrol', 14500, 6000, 2023, 'manual', 105, 889, 0, 2],
            ['KTM', null, 'KTM 390 Duke', 'motorcycle', 'petrol', 5500, 8000, 2023, 'manual', 44, 373, 0, 2],
            ['KTM', null, 'KTM RC 390', 'motorcycle', 'petrol', 6200, 5000, 2023, 'manual', 44, 373, 0, 2],

            // Triumph
            ['Triumph', null, 'Triumph Street Triple RS', 'motorcycle', 'petrol', 12000, 6000, 2023, 'manual', 130, 765, 0, 2],
            ['Triumph', null, 'Triumph Tiger 900 Rally Pro', 'motorcycle', 'petrol', 16000, 10000, 2022, 'manual', 95, 888, 0, 2],
            ['Triumph', null, 'Triumph Bonneville T120', 'cruiser', 'petrol', 13500, 5000, 2023, 'manual', 80, 1200, 0, 2],
            ['Triumph', null, 'Triumph Speed Triple 1200 RS', 'motorcycle', 'petrol', 17500, 4000, 2023, 'manual', 180, 1160, 0, 2],

            // Aprilia
            ['Aprilia', null, 'Aprilia RSV4 1100 Factory', 'motorcycle', 'petrol', 25000, 3000, 2023, 'manual', 217, 1099, 0, 2],
            ['Aprilia', null, 'Aprilia Tuono V4 1100', 'motorcycle', 'petrol', 18000, 5000, 2023, 'manual', 175, 1077, 0, 2],
            ['Aprilia', null, 'Aprilia RS 660', 'motorcycle', 'petrol', 11000, 4000, 2023, 'manual', 100, 659, 0, 2],

            // Budget motorcycles
            ['Yamaha', 'MT-09', 'Yamaha YZF-R3 ABS', 'motorcycle', 'petrol', 4200, 18000, 2020, 'manual', 42, 321, 0, 2],
            ['Kawasaki', 'Z900', 'Kawasaki Ninja 400 ABS', 'motorcycle', 'petrol', 4800, 15000, 2021, 'manual', 49, 399, 0, 2],
            ['Honda', null, 'Honda CBR500R ABS', 'motorcycle', 'petrol', 5500, 12000, 2022, 'manual', 48, 471, 0, 2],
            ['Honda', null, 'Honda CB650R Neo Sports Cafe', 'motorcycle', 'petrol', 8500, 8000, 2022, 'manual', 95, 649, 0, 2],
            ['Honda', null, 'Honda Africa Twin DCT', 'motorcycle', 'petrol', 16000, 15000, 2022, 'automatic', 102, 1084, 0, 2],
            ['BMW', 'Series 1', 'BMW R 1250 GS Adventure', 'motorcycle', 'petrol', 22000, 10000, 2023, 'manual', 136, 1254, 0, 2],
            ['BMW', 'Series 1', 'BMW S 1000 RR', 'motorcycle', 'petrol', 20000, 5000, 2023, 'manual', 207, 999, 0, 2],
        ];

        foreach ($motorcycleSpecs as $spec) {
            $this->createVehicle($spec[0], $spec[1], $spec[2], $spec[3], $spec[4], $spec[5], $spec[6], $spec[7], $spec[8], $spec[9], $spec[10], $spec[11], $spec[12]);
        }
    }

    private function seedTrucks(): void
    {
        $truckSpecs = [
            // Scania
            ['Scania', 'R450', 'Scania R450 Highline Euro 6', 'truck', 'diesel', 78000, 320000, 2020, 'automatic', 450, 12700, 2, 2],
            ['Scania', 'R500', 'Scania R500 Next Generation', 'truck', 'diesel', 95000, 180000, 2022, 'automatic', 500, 12700, 2, 2],
            ['Scania', 'R560', 'Scania S560 Long Haul', 'truck', 'diesel', 110000, 120000, 2023, 'automatic', 560, 16000, 2, 3],
            ['Scania', 'R450', 'Scania P280 Distribution', 'truck', 'diesel', 55000, 250000, 2019, 'automatic', 280, 9300, 2, 2],

            // DAF
            ['DAF', 'XF460', 'DAF XF 460 FT Space Cab', 'truck', 'diesel', 72000, 280000, 2020, 'automatic', 460, 12900, 2, 2],
            ['DAF', 'XF510', 'DAF XG+ 530 FTG', 'truck', 'diesel', 125000, 80000, 2023, 'automatic', 530, 12900, 2, 2],
            ['DAF', 'FTG', 'DAF CF 340 FA Box Truck', 'truck', 'diesel', 65000, 150000, 2021, 'automatic', 340, 10800, 2, 2],
            ['DAF', 'XF460', 'DAF LF 230 Urban Delivery', 'truck', 'diesel', 45000, 120000, 2021, 'automatic', 230, 6700, 2, 2],

            // Iveco
            ['Iveco', null, 'Iveco S-WAY 490 AS', 'truck', 'diesel', 85000, 200000, 2021, 'automatic', 490, 12900, 2, 2],
            ['Iveco', null, 'Iveco Eurocargo 120E25', 'truck', 'diesel', 48000, 180000, 2020, 'automatic', 250, 5880, 2, 2],
            ['Iveco', null, 'Iveco Daily 35S18 Box', 'van', 'diesel', 32000, 85000, 2022, 'manual', 180, 3000, 2, 3],
            ['Iveco', null, 'Iveco Stralis 460 Hi-Way', 'truck', 'diesel', 62000, 350000, 2019, 'automatic', 460, 12900, 2, 2],

            // MAN
            ['MAN', 'TGX 18.440', 'MAN TGX 18.440 XLX', 'truck', 'diesel', 75000, 220000, 2021, 'automatic', 440, 12400, 2, 2],
            ['MAN', 'TGX 18.540', 'MAN TGX 18.540 EfficientLine', 'truck', 'diesel', 98000, 150000, 2022, 'automatic', 540, 12400, 2, 2],
            ['MAN', 'TGX 18.440', 'MAN TGM 15.290 Flatbed', 'truck', 'diesel', 52000, 180000, 2020, 'automatic', 290, 6900, 2, 2],
            ['MAN', 'TGX 18.440', 'MAN TGL 12.250 Box Truck', 'truck', 'diesel', 42000, 120000, 2021, 'automatic', 250, 6900, 2, 2],

            // Volvo Trucks (using Volvo make which is type=car - need special handling)
            // We'll use Mercedes-Benz for some truck entries since we can't easily add van makes
            ['Mercedes-Benz', 'GLE', 'Mercedes Actros 1845 LS', 'truck', 'diesel', 88000, 280000, 2021, 'automatic', 450, 12800, 2, 2],
            ['Mercedes-Benz', 'GLE', 'Mercedes Atego 1224 Box', 'truck', 'diesel', 45000, 150000, 2020, 'automatic', 240, 7700, 2, 2],

            // Mack
            ['Mack', null, 'Mack Anthem 64T Daycab', 'truck', 'diesel', 95000, 180000, 2022, 'automatic', 505, 12800, 2, 2],
            ['Mack', null, 'Mack Granite 64FR Dump', 'truck', 'diesel', 120000, 95000, 2023, 'automatic', 505, 12800, 2, 2],

            // Peterbilt
            ['Peterbilt', null, 'Peterbilt 579 EPIQ', 'truck', 'diesel', 145000, 80000, 2023, 'automatic', 510, 14900, 2, 2],

            // Freightliner
            ['Freightliner', null, 'Freightliner Cascadia Evolution', 'truck', 'diesel', 110000, 150000, 2022, 'automatic', 500, 14800, 2, 2],

            // Budget trucks
            ['DAF', 'XF460', 'DAF XF 460 - Good Condition', 'truck', 'diesel', 28000, 650000, 2016, 'automatic', 460, 12900, 2, 2],
            ['Scania', 'R450', 'Scania G360 Refrigerator', 'truck', 'diesel', 35000, 480000, 2017, 'automatic', 360, 12700, 2, 2],
            ['MAN', 'TGX 18.440', 'MAN TGA 18.480 XXL', 'truck', 'diesel', 22000, 720000, 2015, 'automatic', 480, 12400, 2, 2],
            ['Iveco', null, 'Iveco Daily 35C15 Chassis Cab', 'van', 'diesel', 18000, 120000, 2019, 'manual', 150, 3000, 2, 3],
        ];

        foreach ($truckSpecs as $spec) {
            $this->createVehicle($spec[0], $spec[1], $spec[2], $spec[3], $spec[4], $spec[5], $spec[6], $spec[7], $spec[8], $spec[9], $spec[10], $spec[11], $spec[12]);
        }
    }

    private function seedCaravans(): void
    {
        $caravanSpecs = [
            // Hymer
            ['Hymer', 'Class B', 'Hymer B-MC T 580 Motorhome', 'motorhome', 'diesel', 89000, 15000, 2022, 'automatic', 163, 2200, 2, 4],
            ['Hymer', 'Class S', 'Hymer Exsis-t 580 Pure', 'motorhome', 'diesel', 75000, 22000, 2021, 'automatic', 163, 2200, 2, 4],
            ['Hymer', 'Van', 'Hymer Free 540 Blue Evolution', 'motorhome', 'diesel', 62000, 18000, 2022, 'automatic', 140, 2200, 2, 2],

            // Adria
            ['Adria', 'Action', 'Adria Coral XL Plus 670 SL', 'motorhome', 'diesel', 72000, 20000, 2022, 'automatic', 163, 2200, 2, 4],
            ['Adria', 'Compact', 'Adria Matrix Plus 670 SL', 'motorhome', 'diesel', 68000, 25000, 2021, 'automatic', 163, 2200, 2, 6],
            ['Adria', 'Action', 'Adria Sonic Supreme 710 SL', 'motorhome', 'diesel', 95000, 12000, 2023, 'automatic', 180, 2200, 2, 4],

            // Dethleffs
            ['Dethleffs', null, 'Dethleffs Esprit T 7150 EB', 'motorhome', 'diesel', 82000, 18000, 2022, 'automatic', 163, 2200, 2, 4],
            ['Dethleffs', null, 'Dethleffs Trend T 7057 EB', 'motorhome', 'diesel', 68000, 30000, 2021, 'automatic', 140, 2200, 2, 4],
            ['Dethleffs', null, 'Dethleffs Globebus T1 GT', 'motorhome', 'diesel', 58000, 15000, 2023, 'automatic', 163, 2200, 2, 2],

            // Hobby
            ['Hobby', 'Premium', 'Hobby Optima De Luxe T65 HFL', 'motorhome', 'diesel', 75000, 22000, 2022, 'automatic', 163, 2200, 2, 4],
            ['Hobby', 'A-class', 'Hobby Vantana K65 ET', 'motorhome', 'diesel', 55000, 28000, 2021, 'automatic', 140, 2200, 2, 2],
            ['Hobby', 'Premium', 'Hobby Siesta A 70 QF', 'motorhome', 'diesel', 68000, 15000, 2023, 'automatic', 163, 2200, 2, 6],

            // Carado
            ['Carado', null, 'Carado T449 Motorhome', 'motorhome', 'diesel', 55000, 20000, 2022, 'automatic', 140, 2200, 2, 4],
            ['Carado', null, 'Carado V337 Camper Van', 'motorhome', 'diesel', 48000, 15000, 2023, 'automatic', 140, 2200, 2, 2],

            // Mobilvetta
            ['Mobilvetta', null, 'Mobilvetta K-Silver I59', 'motorhome', 'diesel', 78000, 18000, 2022, 'automatic', 163, 2200, 2, 4],
            ['Mobilvetta', null, 'Mobilvetta Kea P67', 'motorhome', 'diesel', 65000, 25000, 2021, 'automatic', 163, 2200, 2, 4],

            // Rimor
            ['Rimor', null, 'Rimor Seal 695P', 'motorhome', 'diesel', 72000, 12000, 2023, 'automatic', 163, 2200, 2, 6],
            ['Rimor', null, 'Rimor Horus 95 Caravan', 'caravan', 'diesel', 25000, 5000, 2022, 'manual', 0, 0, 0, 4],

            // Eura Mobil
            ['Eura Mobil', null, 'Eura Mobil Integra Line 720 QF', 'motorhome', 'diesel', 92000, 15000, 2023, 'automatic', 180, 2200, 2, 4],
            ['Eura Mobil', null, 'Eura Mobil Profila RS 720 EB', 'motorhome', 'diesel', 85000, 20000, 2022, 'automatic', 163, 2200, 2, 4],

            // LMC
            ['LMC', null, 'LMC Cruiser T732G', 'motorhome', 'diesel', 65000, 22000, 2022, 'automatic', 163, 2200, 2, 4],
            ['LMC', null, 'LMC Breezer V636 Camper', 'motorhome', 'diesel', 52000, 18000, 2023, 'automatic', 140, 2200, 2, 2],

            // Sunlight
            ['Sunlight', null, 'Sunlight T68 Adventure Edition', 'motorhome', 'diesel', 58000, 15000, 2023, 'automatic', 163, 2200, 2, 4],
            ['Sunlight', null, 'Sunlight Cliff 600 Camper Van', 'motorhome', 'diesel', 48000, 10000, 2023, 'automatic', 140, 2200, 2, 2],

            // Budget Caravans
            ['Hymer', 'Van', 'Hymer Van 314 - Budget', 'motorhome', 'diesel', 35000, 65000, 2018, 'manual', 130, 2200, 2, 2],
            ['Adria', 'Compact', 'Adria Twin 540 SP', 'motorhome', 'diesel', 38000, 55000, 2019, 'automatic', 140, 2200, 2, 2],
            ['Hobby', 'A-class', 'Hobby Siesta T55 Budget', 'motorhome', 'diesel', 28000, 80000, 2017, 'manual', 130, 2200, 2, 4],
            ['Dethleffs', null, 'Dethleffs Just T 6812 EB', 'motorhome', 'diesel', 42000, 45000, 2020, 'automatic', 140, 2200, 2, 4],
        ];

        foreach ($caravanSpecs as $spec) {
            $this->createVehicle($spec[0], $spec[1], $spec[2], $spec[3], $spec[4], $spec[5], $spec[6], $spec[7], $spec[8], $spec[9], $spec[10], $spec[11], $spec[12]);
        }
    }

    private function createVehicle(
        string $makeName,
        ?string $modelName,
        string $title,
        string $bodyType,
        string $fuelType,
        int $price,
        int $mileage,
        int $year,
        string $transmission,
        int $power,
        int $engineSize,
        int $doors,
        int $seats
    ): void {
        $make = VehicleMake::where('name', $makeName)->first();
        if (!$make) return;

        // Find model or use first available for this make
        $model = null;
        if ($modelName) {
            $model = VehicleModel::where('make_id', $make->id)->where('name', $modelName)->first();
        }
        if (!$model) {
            $model = VehicleModel::where('make_id', $make->id)->first();
        }
        if (!$model) {
            // Create a generic model
            $model = VehicleModel::create([
                'make_id' => $make->id,
                'name' => 'Others',
                'slug' => Str::slug($makeName . '-others-' . rand(1000, 9999)),
            ]);
        }

        $location = $this->carCities[array_rand($this->carCities)];
        $color = $this->carColors[array_rand($this->carColors)];
        $condition = $mileage < 15000 ? 'new' : 'used';

        // Vary the price slightly
        $priceVariation = $price * (rand(-8, 8) / 100);
        $finalPrice = max(1000, round($price + $priceVariation));

        $features = $this->getRandomFeatures($make->type);

        $vehicle = Vehicle::create([
            'make_id' => $make->id,
            'model_id' => $model->id,
            'title' => $title,
            'description' => $this->generateDescription($title, $makeName, $year, $fuelType, $power, $mileage),
            'price' => $finalPrice,
            'year' => $year,
            'mileage' => $mileage + rand(-2000, 5000),
            'fuel_type' => $fuelType,
            'transmission' => $transmission,
            'body_type' => $bodyType,
            'color' => $color,
            'doors' => $doors,
            'seats' => $seats,
            'engine_size' => $engineSize,
            'power' => $power,
            'country' => $location[0],
            'city' => $location[1],
            'condition' => $condition,
            'status' => 'active',
            'is_featured' => rand(1, 10) <= 2, // 20% chance featured
            'features' => $features,
        ]);

        VehicleImage::create([
            'vehicle_id' => $vehicle->id,
            'image_path' => $this->getStockImageUrl($make->type, $bodyType, $vehicle->id),
            'is_primary' => true,
            'order' => 1,
        ]);
    }

    /**
     * Get a real stock image URL based on vehicle type.
     * Uses Unsplash Source API for realistic vehicle photos.
     */
    private function getStockImageUrl(string $type, string $bodyType, int $vehicleId): string
    {
        // Use Unsplash source API with vehicle-specific search terms
        $searchTerms = match ($type) {
            'car' => match ($bodyType) {
                'sedan' => ['sedan+car', 'luxury+sedan', 'car+front'],
                'suv' => ['suv+car', 'crossover+suv', 'suv+road'],
                'hatchback' => ['hatchback+car', 'compact+car'],
                'wagon' => ['station+wagon', 'estate+car'],
                'convertible' => ['convertible+car', 'cabriolet'],
                'coupe' => ['coupe+car', 'sports+car'],
                default => ['car', 'automobile'],
            },
            'motorcycle' => match ($bodyType) {
                'cruiser' => ['cruiser+motorcycle', 'harley+motorcycle'],
                default => ['motorcycle', 'sportbike', 'motorbike'],
            },
            'truck' => ['semi+truck', 'freight+truck', 'commercial+truck'],
            'caravan' => match ($bodyType) {
                'caravan' => ['caravan+travel', 'camping+trailer'],
                default => ['motorhome', 'rv+camper', 'motorhome+road'],
            },
            default => ['vehicle'],
        };

        $term = $searchTerms[array_rand($searchTerms)];
        // Use unique sig based on vehicle ID for consistent but varied images
        return "https://images.unsplash.com/photo-placeholder?w=800&h=600&q=80&fit=crop&auto=format&sig={$vehicleId}&s={$term}";
    }

    private function getRandomFeatures(string $type): array
    {
        $commonFeatures = ['abs', 'air_conditioning', 'bluetooth', 'cruise_control', 'led_lights'];

        $typeFeatures = match ($type) {
            'car' => ['navigation', 'parking_sensors', 'parking_camera', 'heated_seats', 'leather_seats', 'sunroof', 'alloy_wheels', 'apple_carplay', 'android_auto', 'lane_assist', 'blind_spot_monitor', 'adaptive_cruise_control'],
            'motorcycle' => ['traction_control', 'quick_shifter', 'riding_modes', 'windshield', 'heated_grips', 'luggage_rack', 'center_stand'],
            'truck' => ['retarder', 'sleeping_cab', 'air_suspension', 'fridge', 'gps_tracking', 'tachograph', 'hydraulic_lift'],
            'caravan' => ['solar_panels', 'tv', 'kitchen', 'shower', 'awning', 'bike_rack', 'satellite_dish', 'reversing_camera', 'leveling_system'],
            default => [],
        };

        $allFeatures = array_merge($commonFeatures, $typeFeatures);
        shuffle($allFeatures);

        return array_slice($allFeatures, 0, rand(3, min(8, count($allFeatures))));
    }

    private function generateDescription(string $title, string $make, int $year, string $fuelType, int $power, int $mileage): string
    {
        $fuelLabel = match ($fuelType) {
            'petrol' => 'petrol',
            'diesel' => 'diesel',
            'electric' => 'electric',
            'hybrid' => 'hybrid',
            default => $fuelType,
        };

        $descriptions = [
            "Beautiful {$year} {$title}. This {$fuelLabel} vehicle produces {$power} HP and has been well maintained with full service history. Currently at {$mileage} km. Don't miss this opportunity!",
            "Excellent condition {$title} from {$year}. {$power} HP {$fuelLabel} engine, {$mileage} km on the odometer. All services up to date. Ready for immediate delivery.",
            "Stunning {$make} from {$year} with {$power} HP {$fuelLabel} powerplant. Only {$mileage} km driven. Comes with comprehensive warranty. Contact us for a test drive!",
            "{$year} {$title} in pristine condition. Features {$power} HP {$fuelLabel} engine with {$mileage} km. One owner, non-smoker vehicle. All original documentation available.",
        ];

        return $descriptions[array_rand($descriptions)];
    }
}
