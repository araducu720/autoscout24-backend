<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        static::updating(function (Vehicle $vehicle) {
            // Auto-track price changes when price is modified
            if ($vehicle->isDirty('price') && $vehicle->getOriginal('price') !== null) {
                $oldPrice = (float) $vehicle->getOriginal('price');
                $newPrice = (float) $vehicle->price;

                if ($oldPrice !== $newPrice && $oldPrice > 0) {
                    $history = $vehicle->price_history ?? [];
                    $history[] = [
                        'price' => $oldPrice,
                        'date' => now()->toDateString(),
                    ];
                    // Limit price history to last 50 entries
                    if (count($history) > 50) {
                        $history = array_slice($history, -50);
                    }
                    $vehicle->price_history = $history;
                    $vehicle->price_last_changed_at = now();

                    if ($vehicle->original_price === null) {
                        $vehicle->original_price = $oldPrice;
                    }

                    if ($newPrice < $oldPrice) {
                        $vehicle->price_drops_count = ($vehicle->price_drops_count ?? 0) + 1;
                    }
                }
            }
        });
    }
    
    protected $fillable = [
        'make_id',
        'model_id',
        'user_id',
        'title',
        'description',
        'video_url',
        'price',
        'year',
        'mileage',
        'fuel_type',
        'transmission',
        'drive_type',
        'body_type',
        'color',
        'doors',
        'seats',
        'engine_size',
        'power',
        'emission_class',
        'co2_emissions',
        'fuel_consumption',
        'weight',
        'payload',
        'axle_configuration',
        'previous_owners',
        'accident_free',
        'inspection_valid_until',
        'country',
        'city',
        'condition',
        'status',
        'views_count',
        'is_featured',
        'features',
        'vehicle_condition',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'year' => 'integer',
        'mileage' => 'integer',
        'doors' => 'integer',
        'seats' => 'integer',
        'engine_size' => 'integer',
        'power' => 'integer',
        'views_count' => 'integer',
        'is_featured' => 'boolean',
        'features' => 'array',
        'vehicle_condition' => 'array',
        'price_history' => 'array',
        'price_drops_count' => 'integer',
        'price_last_changed_at' => 'datetime',
        'co2_emissions' => 'integer',
        'fuel_consumption' => 'decimal:1',
        'weight' => 'integer',
        'payload' => 'integer',
        'previous_owners' => 'integer',
        'accident_free' => 'boolean',
        'inspection_valid_until' => 'date',
    ];

    public function make(): BelongsTo
    {
        return $this->belongsTo(VehicleMake::class, 'make_id');
    }

    public function model(): BelongsTo
    {
        return $this->belongsTo(VehicleModel::class, 'model_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(VehicleImage::class);
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(VehicleImage::class)->where('is_primary', true);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function contactMessages(): HasMany
    {
        return $this->hasMany(ContactMessage::class);
    }

    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites');
    }

    public function priceAlerts(): HasMany
    {
        return $this->hasMany(PriceAlert::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function safetradeTransactions(): HasMany
    {
        return $this->hasMany(SafetradeTransaction::class);
    }

    public function testDriveRequests(): HasMany
    {
        return $this->hasMany(TestDriveRequest::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function trackPriceChange(float $newPrice): void
    {
        $oldPrice = (float) $this->price;
        if ($oldPrice === $newPrice) {
            return;
        }

        // Price history tracking is handled automatically by the booted() observer
        $this->update(['price' => $newPrice]);
    }
}
