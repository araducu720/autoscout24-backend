<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'vehicle_id',
        'saved_search_id',
        'target_price',
        'current_price',
        'alert_type',
        'is_active',
        'last_triggered_at',
        'triggered_count',
        'notify_email',
        'notify_push',
    ];

    protected $casts = [
        'target_price' => 'decimal:2',
        'current_price' => 'decimal:2',
        'is_active' => 'boolean',
        'notify_email' => 'boolean',
        'notify_push' => 'boolean',
        'last_triggered_at' => 'datetime',
        'triggered_count' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function savedSearch(): BelongsTo
    {
        return $this->belongsTo(SavedSearch::class);
    }

    public function shouldTrigger(float $newPrice): bool
    {
        if (!$this->is_active) {
            return false;
        }

        return match ($this->alert_type) {
            'below' => $newPrice <= $this->target_price,
            'above' => $newPrice >= $this->target_price,
            'change' => abs($newPrice - $this->current_price) > 0,
            'drop_percent' => $this->current_price > 0
                && (($this->current_price - $newPrice) / $this->current_price * 100) >= $this->target_price,
            default => false,
        };
    }

    public function trigger(float $newPrice): void
    {
        $this->update([
            'current_price' => $newPrice,
            'last_triggered_at' => now(),
            'triggered_count' => $this->triggered_count + 1,
        ]);
    }
}
