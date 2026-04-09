<?php
/**
 * Direct vehicle import endpoint — bypasses Laravel route cache.
 * POST /import.php?key=<BULK_IMPORT_KEY>
 * GET  /import.php?key=<BULK_IMPORT_KEY>&action=status
 */

// Bootstrap Laravel early so we can use env()
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$secret = env('BULK_IMPORT_KEY');
if (empty($secret) || !isset($_GET['key']) || !hash_equals($secret, $_GET['key'])) {
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Forbidden']);
    exit;
}

use App\Models\Vehicle;
use App\Models\VehicleImage;
use App\Models\VehicleMake;
use App\Models\VehicleModel;
use App\Models\Dealer;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

header('Content-Type: application/json');

// Status check
if (isset($_GET['action']) && $_GET['action'] === 'status') {
    echo json_encode([
        'total_vehicles' => Vehicle::count(),
        'active_vehicles' => Vehicle::where('status', 'active')->count(),
        'total_images' => VehicleImage::count(),
        'total_makes' => VehicleMake::count(),
        'total_models' => VehicleModel::count(),
        'total_dealers' => class_exists(Dealer::class) ? Dealer::count() : 0,
    ]);
    exit;
}

// Clear route cache
if (isset($_GET['action']) && $_GET['action'] === 'clear-routes') {
    $cacheDir = __DIR__ . '/../bootstrap/cache';
    $deleted = [];
    foreach (glob($cacheDir . '/routes-*.php') as $file) {
        if (@unlink($file)) $deleted[] = basename($file);
    }
    $hashFile = __DIR__ . '/../storage/framework/route-hash.txt';
    if (file_exists($hashFile)) {
        @unlink($hashFile);
        $deleted[] = 'route-hash.txt';
    }
    echo json_encode(['cleared' => $deleted]);
    exit;
}

// Only accept POST for imports
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Only POST allowed for imports']);
    exit;
}

try {
    // Support both JSON body and form data
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    if (stripos($contentType, 'application/json') !== false) {
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
    } else {
        $input = $_POST;
    }

    // ── Resolve make (by ID or name) ──────────────────────
    $makeId = $input['make_id'] ?? null;
    $makeName = $input['make'] ?? null;
    if (!$makeId && $makeName) {
        $make = VehicleMake::firstOrCreate(
            ['name' => $makeName],
            ['slug' => Str::slug($makeName), 'type' => 'car']
        );
        $makeId = $make->id;
    }

    // ── Resolve model (by ID or name) ─────────────────────
    $modelId = $input['model_id'] ?? null;
    $modelName = $input['model'] ?? null;
    if (!$modelId && $modelName && $makeId) {
        $model = VehicleModel::firstOrCreate(
            ['make_id' => $makeId, 'name' => $modelName],
            ['slug' => Str::slug($modelName)]
        );
        $modelId = $model->id;
    }

    $title = $input['title'] ?? '';
    $description = $input['description'] ?? '';
    $price = floatval($input['price'] ?? 0);
    $year = intval($input['year'] ?? 2024);
    $mileage = intval($input['mileage'] ?? 0);
    $fuelType = $input['fuel_type'] ?? 'petrol';
    $transmission = $input['transmission'] ?? 'manual';
    $bodyType = $input['body_type'] ?? null;
    $color = $input['color'] ?? null;
    $doors = isset($input['doors']) ? intval($input['doors']) : null;
    $seats = isset($input['seats']) ? intval($input['seats']) : null;
    $engineSize = isset($input['engine_size']) ? intval($input['engine_size']) : null;
    $power = isset($input['power']) ? intval($input['power']) : null;
    $country = $input['country'] ?? null;
    $city = $input['city'] ?? null;
    $condition = $input['condition'] ?? 'used';
    $driveType = $input['drive_type'] ?? null;
    $co2Emissions = isset($input['co2_emissions']) ? intval($input['co2_emissions']) : null;
    $emissionClass = $input['emission_class'] ?? null;
    $fuelConsumption = isset($input['fuel_consumption']) ? floatval($input['fuel_consumption']) : null;
    $previousOwners = isset($input['previous_owners']) ? intval($input['previous_owners']) : null;
    $accidentFree = isset($input['accident_free']) ? (bool)$input['accident_free'] : null;

    // Features - accept array or JSON string
    $features = $input['features'] ?? null;
    if (is_string($features)) {
        $features = json_decode($features, true);
    }

    if (!$makeId || !$modelId || !$title) {
        http_response_code(422);
        echo json_encode(['error' => 'Missing required fields: make/make_id, model/model_id, title']);
        exit;
    }

    // ── Dealer handling ───────────────────────────────────
    $userId = 1; // default admin
    $dealerInfo = $input['dealer_info'] ?? null;
    $dealerCreated = false;

    if ($dealerInfo && !empty($dealerInfo['company_name'])) {
        DB::beginTransaction();
        try {
            // Find or create dealer user
            $dealerEmail = $dealerInfo['email'] ?? null;
            $dealerUser = null;

            if ($dealerEmail) {
                $dealerUser = User::where('email', $dealerEmail)->first();
            }

            if (!$dealerUser) {
                // Create dealer user account
                $dealerUser = User::create([
                    'name' => $dealerInfo['company_name'],
                    'email' => $dealerEmail ?: 'dealer_' . Str::random(8) . '@autoscout24.de',
                    'password' => bcrypt(Str::random(32)),
                    'role' => 'dealer',
                    'email_verified_at' => now(),
                ]);
            }

            // Find or create dealer profile
            $dealer = Dealer::where('user_id', $dealerUser->id)->first();
            if (!$dealer) {
                $dealer = Dealer::create([
                    'user_id' => $dealerUser->id,
                    'company_name' => $dealerInfo['company_name'],
                    'slug' => Str::slug($dealerInfo['company_name']) . '-' . Str::random(6),
                    'phone' => $dealerInfo['phone'] ?? null,
                    'email' => $dealerInfo['email'] ?? $dealerUser->email,
                    'website' => $dealerInfo['website'] ?? null,
                    'address' => $dealerInfo['address'] ?? null,
                    'city' => $dealerInfo['city'] ?? $city,
                    'postal_code' => $dealerInfo['postal_code'] ?? null,
                    'country' => $dealerInfo['country'] ?? $country ?? 'DE',
                    'latitude' => $dealerInfo['latitude'] ?? null,
                    'longitude' => $dealerInfo['longitude'] ?? null,
                    'logo' => $dealerInfo['logo'] ?? null,
                    'description' => $dealerInfo['description'] ?? null,
                    'type' => $dealerInfo['type'] ?? 'independent',
                    'is_verified' => true,
                    'is_active' => true,
                ]);
                $dealerCreated = true;
            }

            $userId = $dealerUser->id;
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            // Continue with default admin user
        }
    }

    // ── Create vehicle ────────────────────────────────────
    $vehicle = Vehicle::create([
        'make_id' => $makeId,
        'model_id' => $modelId,
        'user_id' => $userId,
        'title' => Str::limit($title, 250),
        'description' => $description,
        'price' => $price,
        'year' => $year,
        'mileage' => $mileage,
        'fuel_type' => $fuelType,
        'transmission' => $transmission,
        'body_type' => $bodyType,
        'color' => $color,
        'doors' => $doors,
        'seats' => $seats,
        'engine_size' => $engineSize,
        'power' => $power,
        'country' => $country,
        'city' => $city,
        'condition' => $condition,
        'status' => 'active',
        'is_featured' => (bool) rand(0, 9) === 0,
        'views_count' => rand(50, 800),
        'features' => $features,
        'drive_type' => $driveType,
        'co2_emissions' => $co2Emissions,
        'emission_class' => $emissionClass,
        'fuel_consumption' => $fuelConsumption,
        'previous_owners' => $previousOwners,
        'accident_free' => $accidentFree,
    ]);

    // ── Process images ────────────────────────────────────
    $imageCount = 0;

    // Method 1: CDN image URLs (from JSON)
    $imageUrls = $input['image_urls'] ?? [];
    if (is_array($imageUrls) && !empty($imageUrls)) {
        foreach (array_slice($imageUrls, 0, 30) as $index => $imageUrl) {
            if (!is_string($imageUrl) || !filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                continue;
            }
            VehicleImage::create([
                'vehicle_id' => $vehicle->id,
                'image_path' => $imageUrl,
                'is_primary' => $index === 0,
                'order' => $index,
            ]);
            $imageCount++;
        }
    }

    // Method 2: File uploads (multipart form)
    if (!empty($_FILES['images']) && $imageCount === 0) {
        $files = $_FILES['images'];
        $fileCount = is_array($files['name']) ? count($files['name']) : 1;
        
        for ($i = 0; $i < $fileCount; $i++) {
            $tmpName = is_array($files['tmp_name']) ? $files['tmp_name'][$i] : $files['tmp_name'];
            $origName = is_array($files['name']) ? $files['name'][$i] : $files['name'];
            $error = is_array($files['error']) ? $files['error'][$i] : $files['error'];
            
            if ($error !== UPLOAD_ERR_OK) continue;
            
            $ext = pathinfo($origName, PATHINFO_EXTENSION) ?: 'jpg';
            $filename = sprintf('%02d_%s.%s', $i + 1, uniqid(), $ext);
            $dir = "vehicles/{$vehicle->id}";
            
            Storage::disk('public')->makeDirectory($dir);
            
            $path = "{$dir}/{$filename}";
            Storage::disk('public')->put($path, file_get_contents($tmpName));
            
            VehicleImage::create([
                'vehicle_id' => $vehicle->id,
                'image_path' => $path,
                'is_primary' => $i === 0,
                'order' => $i,
            ]);
            $imageCount++;
        }
    }

    echo json_encode([
        'success' => true,
        'vehicle_id' => $vehicle->id,
        'title' => $vehicle->title,
        'images_count' => $imageCount,
        'dealer_created' => $dealerCreated,
        'user_id' => $userId,
    ]);

} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Import failed',
        'message' => config('app.debug') ? $e->getMessage() : 'Internal server error',
    ]);
}
