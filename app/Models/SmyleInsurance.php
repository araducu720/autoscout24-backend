<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmyleInsurance extends Model
{
    protected $table = 'smyle_insurances';

    protected $fillable = [
        'smyle_order_id', 'type', 'status', 'policy_number', 'insurance_provider',
        'start_date', 'end_date', 'coverage_amount',
        'liability_included', 'comprehensive_included',
        'roadside_assistance', 'replacement_vehicle',
        'coverage_details', 'exclusions', 'notes',
        'activated_at', 'expired_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'coverage_amount' => 'decimal:2',
        'liability_included' => 'boolean',
        'comprehensive_included' => 'boolean',
        'roadside_assistance' => 'boolean',
        'replacement_vehicle' => 'boolean',
        'activated_at' => 'datetime',
        'expired_at' => 'datetime',
    ];

    public function smyleOrder(): BelongsTo
    {
        return $this->belongsTo(SmyleOrder::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && $this->end_date && $this->end_date->isFuture();
    }
}
