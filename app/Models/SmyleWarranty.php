<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmyleWarranty extends Model
{
    protected $table = 'smyle_warranties';

    protected $fillable = [
        'smyle_order_id', 'status', 'warranty_number', 'start_date', 'end_date',
        'duration_months', 'engine_covered', 'transmission_covered',
        'electrical_covered', 'suspension_covered', 'brakes_covered', 'ac_covered',
        'max_claim_amount', 'deductible', 'claims_count', 'claims_total',
        'claims_history', 'roadside_assistance', 'towing_included',
        'replacement_mobility', 'terms', 'notes', 'activated_at', 'expired_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'max_claim_amount' => 'decimal:2',
        'deductible' => 'decimal:2',
        'claims_total' => 'decimal:2',
        'claims_history' => 'array',
        'engine_covered' => 'boolean',
        'transmission_covered' => 'boolean',
        'electrical_covered' => 'boolean',
        'suspension_covered' => 'boolean',
        'brakes_covered' => 'boolean',
        'ac_covered' => 'boolean',
        'roadside_assistance' => 'boolean',
        'towing_included' => 'boolean',
        'replacement_mobility' => 'boolean',
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
