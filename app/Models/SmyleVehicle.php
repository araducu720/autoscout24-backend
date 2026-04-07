<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SmyleVehicle extends Model
{
    use SoftDeletes;

    protected $table = 'smyle_vehicles';

    protected $fillable = [
        'vehicle_id', 'is_eligible', 'is_active', 'quality_checked',
        'delivery_base_price', 'location_postal_code', 'location_city',
        'smyle_highlights', 'included_services', 'listed_at', 'delisted_at',
        'rejection_reason',
    ];

    protected $casts = [
        'is_eligible' => 'boolean',
        'is_active' => 'boolean',
        'quality_checked' => 'boolean',
        'delivery_base_price' => 'decimal:2',
        'included_services' => 'array',
        'listed_at' => 'datetime',
        'delisted_at' => 'datetime',
    ];

    // Relationships
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function qualityChecks(): HasMany
    {
        return $this->hasMany(SmyleQualityCheck::class);
    }

    public function latestQualityCheck(): HasOne
    {
        return $this->hasOne(SmyleQualityCheck::class)->latestOfMany();
    }

    public function orders(): HasMany
    {
        return $this->hasMany(SmyleOrder::class);
    }

    // Scopes
    public function scopeEligible($query)
    {
        return $query->where('is_eligible', true)->where('is_active', true);
    }

    public function scopeQualityChecked($query)
    {
        return $query->where('quality_checked', true);
    }

    public function scopeAvailable($query)
    {
        return $query->eligible()
            ->qualityChecked()
            ->whereHas('vehicle', function ($q) {
                $q->where('status', 'active')
                    ->where('year', '>=', now()->subYears(6)->year)
                    ->where('mileage', '<=', 100000);
            });
    }

    // Check if vehicle meets Smyle criteria
    public static function isVehicleEligible(Vehicle $vehicle): bool
    {
        $currentYear = (int) date('Y');
        return $vehicle->status === 'active'
            && $vehicle->year >= ($currentYear - 6)
            && $vehicle->mileage <= 100000
            && $vehicle->condition !== 'damaged';
    }
}
