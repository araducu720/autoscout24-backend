<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_number',
        'buyer_id',
        'seller_id',
        'vehicle_id',
        'total_price',
        'escrow_fee',
        'status',
        'delivery_method',
        'delivery_address',
        'message',
        'payment_deadline',
        'accepted_at',
        'rejected_at',
        'rejection_reason',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
        'escrow_fee' => 'decimal:2',
        'payment_deadline' => 'datetime',
        'accepted_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(6));
            }
            if (empty($order->payment_deadline)) {
                $order->payment_deadline = now()->addHours(48);
            }
        });
    }

    // Relationships
    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function safetradeTransaction(): HasOne
    {
        return $this->hasOne(SafetradeTransaction::class);
    }

    // Actions
    public function accept(): bool
    {
        if ($this->status !== 'pending') return false;

        return $this->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);
    }

    public function reject(string $reason): bool
    {
        if ($this->status !== 'pending') return false;

        return $this->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'rejection_reason' => $reason,
        ]);
    }

    public function cancel(): bool
    {
        if (in_array($this->status, ['completed', 'cancelled', 'rejected'])) return false;

        return $this->update(['status' => 'cancelled']);
    }

    public function complete(): bool
    {
        if ($this->status !== 'accepted') return false;

        return $this->update(['status' => 'completed']);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['completed', 'cancelled', 'rejected']);
    }

    // Accessors
    public function getFormattedPriceAttribute(): string
    {
        return '€' . number_format($this->total_price, 2, ',', '.');
    }
}
