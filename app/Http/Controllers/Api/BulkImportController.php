<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\VehicleImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class BulkImportController extends Controller
{
    /**
     * Validate import key from config/env (not hardcoded).
     */
    private function validateImportKey(Request $request): bool
    {
        $secret = env('BULK_IMPORT_KEY');
        if (empty($secret)) {
            Log::error('BULK_IMPORT_KEY environment variable is not configured');
            return false;
        }
        return hash_equals($secret, (string) $request->query('key'));
    }

    /**
     * Import a single vehicle with images.
     * Requires either: Sanctum auth as admin, OR valid import key.
     * POST /api/v1/import/vehicle?key=...
     */
    public function importVehicle(Request $request)
    {
        // Check auth: admin user OR valid import key
        $user = $request->user();
        $isAdmin = $user && $user->is_admin;
        if (!$isAdmin && !$this->validateImportKey($request)) {
            return response()->json(['error' => 'Unauthorized — admin auth or valid import key required'], 403);
        }

        try {
            $validated = $request->validate([
                'make_id' => 'required|integer|exists:vehicle_makes,id',
                'model_id' => 'required|integer|exists:vehicle_models,id',
                'title' => 'required|string|max:500',
                'description' => 'required|string',
                'price' => 'required|numeric|min:0',
                'year' => 'required|integer|min:1900|max:2030',
                'mileage' => 'required|integer|min:0',
                'fuel_type' => 'required|in:petrol,diesel,electric,hybrid,lpg',
                'transmission' => 'required|in:manual,automatic',
                'body_type' => 'nullable|in:sedan,suv,coupe,hatchback,wagon,convertible,van,pickup,truck,motorcycle,cruiser,motorhome,caravan',
                'color' => 'nullable|string|max:100',
                'doors' => 'nullable|integer|min:0',
                'seats' => 'nullable|integer|min:0',
                'engine_size' => 'nullable|integer|min:0',
                'power' => 'nullable|integer|min:0',
                'country' => 'nullable|string|max:10',
                'city' => 'nullable|string|max:200',
                'condition' => 'nullable|in:new,used',
                'features' => 'nullable|array',
                'images' => 'nullable|array|max:30',
                'images.*' => 'image|mimes:jpeg,jpg,png,webp|max:10240',
            ]);

            // Create the vehicle (assign to authenticated admin, or default admin user id 1)
            $vehicle = Vehicle::create([
                'user_id' => $user?->id ?? 1,
                'make_id' => $validated['make_id'],
                'model_id' => $validated['model_id'],
                'title' => $validated['title'],
                'description' => $validated['description'],
                'price' => $validated['price'],
                'year' => $validated['year'],
                'mileage' => $validated['mileage'],
                'fuel_type' => $validated['fuel_type'],
                'transmission' => $validated['transmission'],
                'body_type' => $validated['body_type'] ?? null,
                'color' => $validated['color'] ?? null,
                'doors' => $validated['doors'] ?? null,
                'seats' => $validated['seats'] ?? null,
                'engine_size' => $validated['engine_size'] ?? null,
                'power' => $validated['power'] ?? null,
                'country' => $validated['country'] ?? null,
                'city' => $validated['city'] ?? null,
                'condition' => $validated['condition'] ?? 'used',
                'status' => 'active',
                'is_featured' => false,
                'views_count' => rand(50, 500),
                'features' => $validated['features'] ?? null,
            ]);

            // Process images
            $imageCount = 0;
            if ($request->hasFile('images')) {
                $files = $request->file('images');
                foreach ($files as $index => $file) {
                    $path = $file->store("vehicles/{$vehicle->id}", 'public');
                    
                    VehicleImage::create([
                        'vehicle_id' => $vehicle->id,
                        'image_path' => $path,
                        'is_primary' => $index === 0,
                        'order' => $index,
                    ]);
                    $imageCount++;
                }
            }

            return response()->json([
                'success' => true,
                'vehicle_id' => $vehicle->id,
                'title' => $vehicle->title,
                'images_uploaded' => $imageCount,
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Import error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Import failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get import status / count of vehicles.
     * Requires either: Sanctum auth as admin, OR valid import key.
     * GET /api/v1/import/status?key=...
     */
    public function status(Request $request)
    {
        $user = $request->user();
        $isAdmin = $user && $user->is_admin;
        if (!$isAdmin && !$this->validateImportKey($request)) {
            return response()->json(['error' => 'Unauthorized — admin auth or valid import key required'], 403);
        }

        return response()->json([
            'total_vehicles' => Vehicle::count(),
            'active_vehicles' => Vehicle::where('status', 'active')->count(),
            'total_images' => VehicleImage::count(),
        ]);
    }
}
