<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class VehicleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $storageUrl = rtrim(config('app.url'), '/') . '/storage';

        return [
            'id' => $this->id,
            'type' => $this->make->type ?? 'car',
            'make' => [
                'id' => $this->make->id,
                'name' => $this->make->name,
                'slug' => $this->make->slug,
                'type' => $this->make->type ?? 'car',
                'logo' => $this->make->logo ? $storageUrl . '/' . $this->make->logo : null,
            ],
            'model' => [
                'id' => $this->model->id,
                'name' => $this->model->name,
                'slug' => $this->model->slug,
            ],
            'title' => $this->title,
            'description' => $this->description,
            'video_url' => $this->video_url,
            'price' => $this->price,
            'year' => $this->year,
            'mileage' => $this->mileage,
            'fuel_type' => $this->fuel_type,
            'transmission' => $this->transmission,
            'body_type' => $this->body_type,
            'color' => $this->color,
            'doors' => $this->doors,
            'seats' => $this->seats,
            'engine_size' => $this->engine_size,
            'power' => $this->power,
            'country' => $this->country,
            'city' => $this->city,
            'condition' => $this->condition,
            'status' => $this->status,
            'views_count' => $this->views_count,
            'is_featured' => $this->is_featured,
            'features' => $this->features ?? [],
            'vehicle_condition' => $this->vehicle_condition ?? [],
            'images' => $this->images
                ->filter(fn($image) => !str_contains($image->image_path, 'placeholder/'))
                ->map(fn($image) => [
                    'id' => $image->id,
                    'path' => $image->image_path,
                    'url' => str_starts_with($image->image_path, 'http')
                        ? $image->image_path
                        : $storageUrl . '/' . $image->image_path,
                    'is_primary' => $image->is_primary,
                    'order' => $image->order,
                ])->values(),
            'primary_image' => ($this->primaryImage?->image_path && !str_contains($this->primaryImage->image_path, 'placeholder/'))
                ? (str_starts_with($this->primaryImage->image_path, 'http')
                    ? $this->primaryImage->image_path
                    : $storageUrl . '/' . $this->primaryImage->image_path)
                : null,
            'seller' => $this->whenLoaded('user', function () use ($storageUrl) {
                $user = $this->user;
                if (!$user) return null;
                $dealer = $user->relationLoaded('dealer') ? $user->dealer : null;
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'avatar' => $user->avatar ? (str_starts_with($user->avatar, 'http') ? $user->avatar : $storageUrl . '/' . $user->avatar) : null,
                    'member_since' => $user->created_at?->toISOString(),
                    'country' => $user->country,
                    'is_dealer' => $dealer !== null,
                    'dealer' => $dealer ? [
                        'id' => $dealer->id,
                        'company_name' => $dealer->company_name,
                        'slug' => $dealer->slug,
                        'logo' => $dealer->logo ? (str_starts_with($dealer->logo, 'http') ? $dealer->logo : $storageUrl . '/' . $dealer->logo) : null,
                        'description' => $dealer->description,
                        'address' => $dealer->address,
                        'city' => $dealer->city,
                        'postal_code' => $dealer->postal_code,
                        'country' => $dealer->country,
                        'phone' => $dealer->phone,
                        'email' => $dealer->email,
                        'website' => $dealer->website,
                        'is_verified' => $dealer->is_verified,
                        'rating' => $dealer->rating,
                        'total_reviews' => $dealer->total_reviews,
                        'offers_financing' => $dealer->offers_financing,
                        'offers_warranty' => $dealer->offers_warranty,
                        'offers_home_delivery' => $dealer->offers_home_delivery,
                    ] : null,
                ];
            }),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
