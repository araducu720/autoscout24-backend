<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmyleDelivery extends Model
{
    protected $table = 'smyle_deliveries';

    protected $fillable = [
        'smyle_order_id', 'tracking_number', 'status',
        'driver_name', 'driver_phone', 'transport_company',
        'pickup_postal_code', 'pickup_city', 'pickup_address',
        'delivery_postal_code', 'delivery_city', 'delivery_address',
        'distance_km', 'delivery_cost',
        'scheduled_pickup_date', 'scheduled_delivery_date', 'delivery_time_slot',
        'actual_pickup_at', 'actual_delivery_at',
        'delivery_notes', 'handover_checklist', 'buyer_signature_path', 'delivery_photos',
    ];

    protected $casts = [
        'delivery_cost' => 'decimal:2',
        'scheduled_pickup_date' => 'date',
        'scheduled_delivery_date' => 'date',
        'actual_pickup_at' => 'datetime',
        'actual_delivery_at' => 'datetime',
        'handover_checklist' => 'array',
        'delivery_photos' => 'array',
    ];

    public function smyleOrder(): BelongsTo
    {
        return $this->belongsTo(SmyleOrder::class);
    }

    // Calculate delivery cost based on distance
    public static function calculateCost(string $fromPostalCode, string $toPostalCode): float
    {
        $fromRegion = (int) substr($fromPostalCode, 0, 2);
        $toRegion = (int) substr($toPostalCode, 0, 2);
        $regionDiff = abs($fromRegion - $toRegion);

        if ($regionDiff <= 5) return 599.00;
        if ($regionDiff <= 15) return 749.00;
        if ($regionDiff <= 30) return 899.00;
        return 999.00;
    }
}
