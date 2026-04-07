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
use Illuminate\Support\Facades\Storage;

header('Content-Type: application/json');

// Status check
if (isset($_GET['action']) && $_GET['action'] === 'status') {
    echo json_encode([
        'total_vehicles' => Vehicle::count(),
        'active_vehicles' => Vehicle::where('status', 'active')->count(),
        'total_images' => VehicleImage::count(),
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
    // Parse form data
    $makeId = $_POST['make_id'] ?? null;
    $modelId = $_POST['model_id'] ?? null;
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = floatval($_POST['price'] ?? 0);
    $year = intval($_POST['year'] ?? 2024);
    $mileage = intval($_POST['mileage'] ?? 0);
    $fuelType = $_POST['fuel_type'] ?? 'petrol';
    $transmission = $_POST['transmission'] ?? 'manual';
    $bodyType = $_POST['body_type'] ?? null;
    $color = $_POST['color'] ?? null;
    $doors = isset($_POST['doors']) ? intval($_POST['doors']) : null;
    $seats = isset($_POST['seats']) ? intval($_POST['seats']) : null;
    $engineSize = isset($_POST['engine_size']) ? intval($_POST['engine_size']) : null;
    $power = isset($_POST['power']) ? intval($_POST['power']) : null;
    $country = $_POST['country'] ?? null;
    $city = $_POST['city'] ?? null;
    $condition = $_POST['condition'] ?? 'used';
    $features = isset($_POST['features']) ? json_decode($_POST['features'], true) : null;

    if (!$makeId || !$modelId || !$title) {
        http_response_code(422);
        echo json_encode(['error' => 'Missing required fields: make_id, model_id, title']);
        exit;
    }

    // Create vehicle
    $vehicle = Vehicle::create([
        'make_id' => $makeId,
        'model_id' => $modelId,
        'title' => $title,
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
        'is_featured' => false,
        'views_count' => rand(50, 500),
        'features' => $features,
    ]);

    // Process uploaded images
    $imageCount = 0;
    if (!empty($_FILES['images'])) {
        $files = $_FILES['images'];
        $fileCount = is_array($files['name']) ? count($files['name']) : 1;
        
        for ($i = 0; $i < $fileCount; $i++) {
            $tmpName = is_array($files['tmp_name']) ? $files['tmp_name'][$i] : $files['tmp_name'];
            $origName = is_array($files['name']) ? $files['name'][$i] : $files['name'];
            $error = is_array($files['error']) ? $files['error'][$i] : $files['error'];
            
            if ($error !== UPLOAD_ERR_OK) continue;
            
            // Store via Laravel Storage
            $ext = pathinfo($origName, PATHINFO_EXTENSION) ?: 'jpg';
            $filename = sprintf('%02d_%s.%s', $i + 1, uniqid(), $ext);
            $dir = "vehicles/{$vehicle->id}";
            
            // Ensure directory exists
            Storage::disk('public')->makeDirectory($dir);
            
            // Move file
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
        'images_uploaded' => $imageCount,
    ]);

} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Import failed',
        'message' => config('app.debug') ? $e->getMessage() : 'Internal server error',
    ]);
}
