<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(\Illuminate\Http\Request::capture());

header('Content-Type: application/json');

$types = \Illuminate\Support\Facades\DB::table('vehicles')
    ->select('type', \Illuminate\Support\Facades\DB::raw('count(*) as cnt'))
    ->groupBy('type')
    ->get();

$bodyTypes = \Illuminate\Support\Facades\DB::table('vehicles')
    ->select('body_type', \Illuminate\Support\Facades\DB::raw('count(*) as cnt'))
    ->whereNotNull('body_type')
    ->groupBy('body_type')
    ->get();

$fuelTypes = \Illuminate\Support\Facades\DB::table('vehicles')
    ->select('fuel_type', \Illuminate\Support\Facades\DB::raw('count(*) as cnt'))
    ->whereNotNull('fuel_type')
    ->groupBy('fuel_type')
    ->get();

echo json_encode([
    'vehicle_types' => $types,
    'body_types' => $bodyTypes,
    'fuel_types' => $fuelTypes,
], JSON_PRETTY_PRINT);
