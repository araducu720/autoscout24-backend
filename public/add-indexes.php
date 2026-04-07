<?php
/**
 * Temporary script to add performance indexes.
 * Run once, then delete.
 */

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

header('Content-Type: application/json');
$results = [];

// Vehicles indexes — add individually to skip already-existing ones
$vehicleIndexes = [
    'is_featured' => 'idx_vehicles_is_featured',
    'fuel_type' => 'idx_vehicles_fuel_type',
    'transmission' => 'idx_vehicles_transmission',
    'body_type' => 'idx_vehicles_body_type',
    'condition' => 'idx_vehicles_condition',
    'country' => 'idx_vehicles_country',
    'created_at' => 'idx_vehicles_created_at',
];
$vehicleOk = 0;
foreach ($vehicleIndexes as $col => $name) {
    try {
        Schema::table('vehicles', function (Blueprint $table) use ($col, $name) {
            $table->index($col, $name);
        });
        $vehicleOk++;
    } catch (\Exception $e) {
        // already exists, skip
    }
}
$results[] = "vehicles: $vehicleOk new indexes added";

// Users indexes
try {
    Schema::table('users', function (Blueprint $table) {
        $table->index('is_admin', 'idx_users_is_admin');
    });
    $results[] = 'users: 1 index OK';
} catch (\Exception $e) {
    $results[] = 'users: ' . $e->getMessage();
}

// Conversations indexes
try {
    Schema::table('conversations', function (Blueprint $table) {
        $table->index('buyer_id', 'idx_conversations_buyer_id');
        $table->index('seller_id', 'idx_conversations_seller_id');
    });
    $results[] = 'conversations: 2 indexes OK';
} catch (\Exception $e) {
    $results[] = 'conversations: ' . $e->getMessage();
}

// Phone reveals indexes
try {
    Schema::table('phone_reveals', function (Blueprint $table) {
        $table->index('created_at', 'idx_phone_reveals_created_at');
    });
    $results[] = 'phone_reveals: 1 index OK';
} catch (\Exception $e) {
    $results[] = 'phone_reveals: ' . $e->getMessage();
}

// Contact messages indexes
try {
    Schema::table('contact_messages', function (Blueprint $table) {
        $table->index('status', 'idx_contact_messages_status');
    });
    $results[] = 'contact_messages: 1 index OK';
} catch (\Exception $e) {
    $results[] = 'contact_messages: ' . $e->getMessage();
}

// Invoices indexes
try {
    Schema::table('invoices', function (Blueprint $table) {
        $table->index('status', 'idx_invoices_status');
    });
    $results[] = 'invoices: 1 index OK';
} catch (\Exception $e) {
    $results[] = 'invoices: ' . $e->getMessage();
}

echo json_encode(['results' => $results], JSON_PRETTY_PRINT);
