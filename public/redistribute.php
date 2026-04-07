<?php
/**
 * Vehicle Redistribution Script
 * Redistributes all vehicles evenly across all dealers using round-robin.
 * 
 * Usage: https://adminautoscout.dev/redistribute.php?key=redistribute2025
 */

// Security key check
if (($_GET['key'] ?? '') !== 'redistribute2025') {
    http_response_code(403);
    echo json_encode(['error' => 'Invalid key']);
    exit;
}

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

header('Content-Type: application/json');

try {
    $db = app('db');
    
    // Get all dealers with their user_ids
    $dealers = $db->table('dealers')
        ->where('is_active', true)
        ->orderBy('id')
        ->get(['id', 'user_id', 'company_name']);
    
    $dealerCount = $dealers->count();
    if ($dealerCount === 0) {
        echo json_encode(['error' => 'No active dealers found']);
        exit;
    }
    
    // Get all vehicles
    $vehicles = $db->table('vehicles')->orderBy('id')->get(['id', 'user_id', 'title']);
    $vehicleCount = $vehicles->count();
    
    if ($vehicleCount === 0) {
        echo json_encode(['error' => 'No vehicles found']);
        exit;
    }
    
    // Current distribution stats (before)
    $beforeStats = $db->table('vehicles')
        ->join('dealers', 'vehicles.user_id', '=', 'dealers.user_id')
        ->selectRaw('dealers.company_name, dealers.id as dealer_id, COUNT(vehicles.id) as vehicle_count')
        ->groupBy('dealers.id', 'dealers.company_name')
        ->orderByDesc('vehicle_count')
        ->get();
    
    $dealersWithZero = $dealerCount - $beforeStats->count();
    $maxBefore = $beforeStats->first()->vehicle_count ?? 0;
    $minBefore = $beforeStats->last()->vehicle_count ?? 0;
    
    // Shuffle vehicles for variety
    $vehicleIds = $vehicles->pluck('id')->shuffle()->values()->toArray();
    $dealerUserIds = $dealers->pluck('user_id')->toArray();
    
    // Round-robin assignment
    $assignments = [];
    foreach ($vehicleIds as $index => $vehicleId) {
        $dealerIndex = $index % $dealerCount;
        $assignments[] = [
            'vehicle_id' => $vehicleId,
            'user_id' => $dealerUserIds[$dealerIndex],
        ];
    }
    
    // Apply assignments in a transaction
    $db->beginTransaction();
    try {
        foreach ($assignments as $assignment) {
            $db->table('vehicles')
                ->where('id', $assignment['vehicle_id'])
                ->update(['user_id' => $assignment['user_id']]);
        }
        $db->commit();
    } catch (\Throwable $e) {
        $db->rollBack();
        echo json_encode([
            'error' => 'Transaction failed: ' . $e->getMessage()
        ]);
        exit;
    }
    
    // After distribution stats
    $afterStats = $db->table('vehicles')
        ->join('dealers', 'vehicles.user_id', '=', 'dealers.user_id')
        ->selectRaw('dealers.company_name, dealers.id as dealer_id, COUNT(vehicles.id) as vehicle_count')
        ->groupBy('dealers.id', 'dealers.company_name')
        ->orderByDesc('vehicle_count')
        ->get();
    
    $vehiclesPerDealer = floor($vehicleCount / $dealerCount);
    $remainder = $vehicleCount % $dealerCount;
    
    // Build per-dealer breakdown
    $dealerBreakdown = [];
    foreach ($afterStats as $stat) {
        $dealerBreakdown[] = [
            'dealer_id' => $stat->dealer_id,
            'company' => $stat->company_name,
            'vehicles' => $stat->vehicle_count,
        ];
    }
    
    echo json_encode([
        'success' => true,
        'summary' => [
            'total_vehicles' => $vehicleCount,
            'total_dealers' => $dealerCount,
            'vehicles_per_dealer' => $vehiclesPerDealer,
            'dealers_with_extra' => $remainder,
            'before' => [
                'max_vehicles' => $maxBefore,
                'min_vehicles' => $minBefore,
                'dealers_with_zero' => $dealersWithZero,
            ],
            'after' => [
                'max_vehicles' => $afterStats->first()->vehicle_count ?? 0,
                'min_vehicles' => $afterStats->last()->vehicle_count ?? 0,
                'dealers_with_zero' => $dealerCount - $afterStats->count(),
            ],
        ],
        'top_10_dealers' => array_slice($dealerBreakdown, 0, 10),
        'bottom_10_dealers' => array_slice($dealerBreakdown, -10),
    ], JSON_PRETTY_PRINT);
    
} catch (\Throwable $e) {
    echo json_encode([
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
    ]);
}
