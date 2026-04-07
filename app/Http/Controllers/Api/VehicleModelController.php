<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\VehicleModelResource;
use App\Models\VehicleModel;
use Illuminate\Http\Request;

class VehicleModelController extends Controller
{
    public function index(Request $request)
    {
        $query = VehicleModel::query();

        // Filter by make_id (required for dependent select)
        if ($request->has('make_id')) {
            $query->where('make_id', $request->make_id);
        }

        $models = $query->orderBy('name')->get();

        return VehicleModelResource::collection($models);
    }

    public function show($id)
    {
        $model = VehicleModel::findOrFail($id);
        return new VehicleModelResource($model);
    }
}
