<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\VehicleMakeResource;
use App\Models\VehicleMake;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class VehicleMakeController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->input('type');
        $search = $request->input('search');
        $limit = min((int) $request->input('limit', 500), 1000);
        $withVehicles = $request->boolean('with_vehicles');

        $cacheKey = 'makes:' . md5(serialize([$type, $search, $limit, $withVehicles]));

        $makes = Cache::remember($cacheKey, 300, function () use ($type, $search, $limit, $withVehicles) {
            $query = VehicleMake::query();

            if ($type) {
                $query->where('type', $type);
            }

            if ($search) {
                $query->where('name', 'like', $search . '%');
            }

            if ($withVehicles) {
                $query->whereHas('vehicles');
            }

            $query->withCount(['models', 'vehicles']);

            return $query->orderByDesc('vehicles_count')
                ->orderBy('name')
                ->limit($limit)
                ->get();
        });

        return VehicleMakeResource::collection($makes);
    }

    public function show($id)
    {
        $make = VehicleMake::withCount('models')->findOrFail($id);
        return new VehicleMakeResource($make);
    }

    /**
     * Get all models for a specific make.
     */
    public function models($makeId)
    {
        $make = VehicleMake::findOrFail($makeId);

        $models = $make->models()
            ->withCount('vehicles')
            ->orderBy('name')
            ->get();

        return response()->json([
            'data' => $models,
        ]);
    }
}
