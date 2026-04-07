<?php

namespace App\Console\Commands;

use App\Models\Vehicle;
use App\Models\VehicleMake;
use App\Models\VehicleModel;
use App\Models\VehicleImage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportScrapedListings extends Command
{
    protected $signature = 'import:listings {path : Path to the listings cache directory}';
    protected $description = 'Import scraped AutoScout24 listings with full details and images';

    /**
     * Map scraped body types to DB enum values.
     */
    private array $bodyTypeMap = [
        'Sedans'                => 'sedan',
        'SUV/Off-Road/Pick-up'  => 'suv',
        'Coupe'                 => 'coupe',
        'Compact'               => 'hatchback',
        'Station wagon'         => 'wagon',
        'Convertible'           => 'convertible',
        'Van'                   => 'van',
        'Other'                 => null,
    ];

    /**
     * Map scraped fuel types to DB enum values.
     */
    private array $fuelTypeMap = [
        'Gasoline'          => 'petrol',
        'Diesel'            => 'diesel',
        'Electric'          => 'electric',
        'Electric/Gasoline' => 'hybrid',
        'Electric/Diesel'   => 'hybrid',
        'LPG'               => 'lpg',
    ];

    public function handle(): int
    {
        $path = $this->argument('path');

        if (! is_dir($path)) {
            $this->error("Directory not found: $path");
            return 1;
        }

        $dirs = array_filter(scandir($path), function ($d) use ($path) {
            return $d !== '.' && $d !== '..' && is_dir("$path/$d");
        });

        $total = count($dirs);
        $this->info("Found $total listing directories to import.");

        // Ensure vehicles storage directory exists
        Storage::disk('public')->makeDirectory('vehicles');

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $imported = 0;
        $skipped  = 0;
        $errors   = 0;

        foreach ($dirs as $dir) {
            $listingPath = "$path/$dir";
            $jsonFile    = "$listingPath/details.json";

            if (! file_exists($jsonFile)) {
                $this->newLine();
                $this->warn("No details.json in $dir — skipping.");
                $skipped++;
                $bar->advance();
                continue;
            }

            try {
                $this->importListing($listingPath, $dir);
                $imported++;
            } catch (\Throwable $e) {
                $this->newLine();
                $this->error("Error importing $dir: {$e->getMessage()}");
                $errors++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Import complete: $imported imported, $skipped skipped, $errors errors.");

        return 0;
    }

    private function importListing(string $listingPath, string $dirName): void
    {
        $json = json_decode(file_get_contents("$listingPath/details.json"), true);

        if (! $json) {
            throw new \RuntimeException('Invalid JSON');
        }

        $listing = $json['props']['pageProps']['listingDetails'] ?? null;
        if (! $listing) {
            throw new \RuntimeException('listingDetails not found in JSON');
        }

        $vehicle  = $listing['vehicle'] ?? [];
        $prices   = $listing['prices'] ?? [];
        $location = $listing['location'] ?? [];

        // ── Make ───────────────────────────────────────────
        $makeName = $vehicle['make'] ?? 'Unknown';
        $make = VehicleMake::firstOrCreate(
            ['name' => $makeName],
            [
                'slug' => Str::slug($makeName),
                'type' => 'car',
            ]
        );

        // ── Model ──────────────────────────────────────────
        $modelName = $vehicle['model'] ?? $vehicle['modelGroup'] ?? 'Unknown';
        $model = VehicleModel::firstOrCreate(
            ['make_id' => $make->id, 'name' => $modelName],
            ['slug' => Str::slug($modelName)]
        );

        // ── Extract fields ─────────────────────────────────
        $priceRaw = $prices['public']['priceRaw']
            ?? $prices['dealer']['priceRaw']
            ?? 0;

        $firstRegDate = $vehicle['firstRegistrationDateRaw'] ?? null;
        $year = $firstRegDate ? (int) substr($firstRegDate, 0, 4) : (int) date('Y');
        if ($year < 1900 || $year > 2030) $year = (int) date('Y');

        $mileage = (int) ($vehicle['mileageInKmRaw'] ?? 0);

        $fuelRaw = $vehicle['fuelCategory']['formatted'] ?? 'Gasoline';
        $fuelType = $this->fuelTypeMap[$fuelRaw] ?? 'petrol';

        $transmission = strtolower($vehicle['transmissionType'] ?? 'manual');
        if (! in_array($transmission, ['manual', 'automatic'])) {
            $transmission = 'manual';
        }

        $bodyRaw  = $vehicle['bodyType'] ?? null;
        $bodyType = $this->bodyTypeMap[$bodyRaw] ?? null;

        $color = $vehicle['bodyColor'] ?? null;
        $doors = $vehicle['numberOfDoors'] ?? null;
        $seats = $vehicle['numberOfSeats'] ?? null;
        $engineSize = $vehicle['rawDisplacementInCCM'] ?? null;
        $power = $vehicle['rawPowerInHp'] ?? null;
        $country = $location['countryCode'] ?? null;
        $city = $location['city'] ?? null;

        $condition = ($mileage <= 100) ? 'new' : 'used';

        // ── Title ──────────────────────────────────────────
        $title = $vehicle['modelVersionInput']
            ?? "{$makeName} {$modelName} {$year}";

        // ── Description ────────────────────────────────────
        $description = $listing['description'] ?? '';
        // Clean HTML tags but keep line breaks
        $description = str_replace(['<br />', '<br>', '<br/>'], "\n", $description);
        $description = strip_tags(html_entity_decode($description, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        $description = trim($description);
        if (empty($description)) {
            $description = "$makeName $modelName — $year, $mileage km";
        }

        // ── Features (from vehicle specs) ──────────────────
        $features = [];
        $specDetails = $vehicle['vehicleDetails'] ?? $listing['vehicleDetails'] ?? [];

        if (is_array($specDetails)) {
            foreach ($specDetails as $section) {
                if (isset($section['data']) && is_array($section['data'])) {
                    foreach ($section['data'] as $item) {
                        if (isset($item['translated'])) {
                            $features[] = $item['translated'];
                        } elseif (isset($item['label']) && isset($item['value'])) {
                            $features[] = $item['label'] . ': ' . $item['value'];
                        }
                    }
                }
            }
        }

        // Also extract from specs if available
        $specs = $listing['specifications'] ?? [];
        if (is_array($specs)) {
            foreach ($specs as $specGroup) {
                if (isset($specGroup['items']) && is_array($specGroup['items'])) {
                    foreach ($specGroup['items'] as $item) {
                        if (isset($item['label'], $item['value'])) {
                            $features[] = $item['label'] . ': ' . $item['value'];
                        }
                    }
                }
            }
        }

        // ── Create vehicle ─────────────────────────────────
        DB::beginTransaction();
        try {
            $vehicleRecord = Vehicle::create([
                'make_id'      => $make->id,
                'model_id'     => $model->id,
                'user_id'      => 1, // admin user
                'title'        => Str::limit($title, 250),
                'description'  => $description,
                'price'        => $priceRaw,
                'year'         => $year,
                'mileage'      => $mileage,
                'fuel_type'    => $fuelType,
                'transmission' => $transmission,
                'body_type'    => $bodyType,
                'color'        => $color,
                'doors'        => $doors,
                'seats'        => $seats,
                'engine_size'  => $engineSize,
                'power'        => $power,
                'country'      => $country,
                'city'         => $city,
                'condition'    => $condition,
                'status'       => 'active',
                'views_count'  => rand(10, 500),
                'is_featured'  => (bool) rand(0, 5) === 0, // ~20% featured
                'features'     => ! empty($features) ? $features : null,
            ]);

            // ── Images (large JPG) ────────────────────────
            $imageFiles = glob("$listingPath/image_*_large.jpg");
            natsort($imageFiles);

            $order = 0;
            foreach ($imageFiles as $imageFile) {
                $ext = pathinfo($imageFile, PATHINFO_EXTENSION);
                $storageName = "vehicles/{$vehicleRecord->id}_" . Str::random(8) . "_{$order}.{$ext}";

                // Copy image to storage
                Storage::disk('public')->put(
                    $storageName,
                    file_get_contents($imageFile)
                );

                VehicleImage::create([
                    'vehicle_id' => $vehicleRecord->id,
                    'image_path' => $storageName,
                    'is_primary' => $order === 0,
                    'order'      => $order,
                ]);

                $order++;
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
