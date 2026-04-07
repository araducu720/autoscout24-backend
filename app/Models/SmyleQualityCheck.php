<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class SmyleQualityCheck extends Model
{
    protected $table = 'smyle_quality_checks';

    protected $fillable = [
        'smyle_vehicle_id', 'inspector_id', 'reference', 'status',
        'overall_score', 'exterior_check', 'interior_check', 'engine_check',
        'electronics_check', 'tires_brakes_check', 'documents_check',
        'inspector_notes', 'issues_found', 'photos', 'roadworthy',
        'inspection_date', 'valid_until',
    ];

    protected $casts = [
        'exterior_check' => 'array',
        'interior_check' => 'array',
        'engine_check' => 'array',
        'electronics_check' => 'array',
        'tires_brakes_check' => 'array',
        'documents_check' => 'array',
        'issues_found' => 'array',
        'photos' => 'array',
        'roadworthy' => 'boolean',
        'inspection_date' => 'date',
        'valid_until' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($check) {
            if (empty($check->reference)) {
                $check->reference = 'AS24-QC-' . strtoupper(base_convert(time(), 10, 36)) . '-' . strtoupper(Str::random(4));
            }
        });
    }

    public function smyleVehicle(): BelongsTo
    {
        return $this->belongsTo(SmyleVehicle::class);
    }

    public function inspector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }

    public function isPassed(): bool
    {
        return $this->status === 'passed';
    }

    public function isValid(): bool
    {
        return $this->isPassed() && $this->valid_until && $this->valid_until->isFuture();
    }
}
