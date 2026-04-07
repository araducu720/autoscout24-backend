<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SavedSearch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SavedSearchController extends Controller
{
    public function index(Request $request)
    {
        $savedSearches = SavedSearch::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($savedSearches);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'filters' => 'required|array',
            'filters.make_id' => 'nullable|integer|exists:vehicle_makes,id',
            'filters.model_id' => 'nullable|integer|exists:vehicle_models,id',
            'filters.type' => 'nullable|string|in:car,truck,motorcycle,van,caravan',
            'filters.price_min' => 'nullable|numeric|min:0',
            'filters.price_max' => 'nullable|numeric|min:0',
            'filters.year_min' => 'nullable|integer|min:1900|max:2030',
            'filters.year_max' => 'nullable|integer|min:1900|max:2030',
            'filters.mileage_min' => 'nullable|integer|min:0',
            'filters.mileage_max' => 'nullable|integer|min:0',
            'filters.fuel_type' => 'nullable|string|in:petrol,diesel,electric,hybrid,lpg,cng,hydrogen,other',
            'filters.transmission' => 'nullable|string|in:manual,automatic,semi-automatic',
            'filters.condition' => 'nullable|string|in:new,used,certified',
            'filters.country' => 'nullable|string|max:5',
            'notify_new_matches' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $savedSearch = SavedSearch::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'filters' => $request->filters,
            'notify_new_matches' => $request->notify_new_matches ?? false,
        ]);

        return response()->json([
            'message' => 'Search saved successfully',
            'data' => $savedSearch,
        ], 201);
    }

    public function show($id)
    {
        $savedSearch = SavedSearch::where('user_id', auth()->id())
            ->findOrFail($id);

        return response()->json($savedSearch);
    }

    public function update(Request $request, $id)
    {
        $savedSearch = SavedSearch::where('user_id', auth()->id())
            ->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'filters' => 'sometimes|array',
            'notify_new_matches' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $savedSearch->update($request->only(['name', 'filters', 'notify_new_matches']));

        return response()->json([
            'message' => 'Search updated successfully',
            'data' => $savedSearch,
        ]);
    }

    public function destroy($id)
    {
        $savedSearch = SavedSearch::where('user_id', auth()->id())
            ->findOrFail($id);

        $savedSearch->delete();

        return response()->json([
            'message' => 'Search deleted successfully',
        ]);
    }
}
