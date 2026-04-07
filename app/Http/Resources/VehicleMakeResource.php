<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleMakeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'logo' => $this->logo,
            'type' => $this->type,
            'models_count' => $this->when($this->models_count !== null, $this->models_count),
            'vehicles_count' => $this->when($this->vehicles_count !== null, $this->vehicles_count),
        ];
    }
}
