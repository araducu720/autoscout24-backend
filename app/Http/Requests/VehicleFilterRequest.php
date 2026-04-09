<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VehicleFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => 'nullable|string|max:255',
            'make_id' => 'nullable|integer|exists:vehicle_makes,id',
            'model_id' => 'nullable|integer|exists:vehicle_models,id',
            'price_min' => 'nullable|numeric|min:0|max:100000000',
            'price_max' => 'nullable|numeric|min:0|max:100000000',
            'year_min' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'year_max' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'mileage_min' => 'nullable|integer|min:0|max:10000000',
            'mileage_max' => 'nullable|integer|min:0|max:10000000',
            'engine_size_min' => 'nullable|integer|min:0|max:100000',
            'engine_size_max' => 'nullable|integer|min:0|max:100000',
            'power_min' => 'nullable|integer|min:0|max:10000',
            'power_max' => 'nullable|integer|min:0|max:10000',
            'fuel_type' => 'nullable|string|in:petrol,diesel,electric,hybrid,lpg,cng,hydrogen,ethanol,other',
            'transmission' => 'nullable|string|in:manual,automatic,semi-automatic',
            'body_type' => 'nullable|string|in:sedan,wagon,hatchback,suv,coupe,convertible,van,truck,pickup,panel_van,box_van,chassis_cab,tipper,flatbed,tractor_unit,reefer,tanker,curtainsider,naked,enduro,chopper,scooter,sport,touring_bike,quad,motorhome,caravan,other',
            'doors' => 'nullable|integer|min:1|max:10',
            'seats' => 'nullable|integer|min:1|max:50',
            'condition' => 'nullable|string|in:new,used,certified,damaged',
            'color' => 'nullable|string|max:50',
            'country' => 'nullable|string|max:5',
            'city' => 'nullable|string|max:100',
            'seller_type' => 'nullable|string|in:dealer,private',
            'type' => 'nullable|string|in:car,motorcycle,truck,caravan,van,utility,atv,trailer,construction,agricultural,forklift,bus',
            'features' => 'nullable',
            'vehicle_condition' => 'nullable',
            'drive_type' => 'nullable|string|in:fwd,rwd,awd,4wd',
            'emission_class' => 'nullable|string|in:euro1,euro2,euro3,euro4,euro5,euro5b,euro6,euro6b,euro6c,euro6d,euro6d-temp',
            'accident_free' => 'nullable|boolean',
            'featured' => 'nullable|boolean',
            'sort_by' => 'nullable|string|in:price,year,mileage,created_at,views_count',
            'sort_order' => 'nullable|string|in:asc,desc',
            'per_page' => 'nullable|integer|min:1|max:100',
        ];
    }
}
