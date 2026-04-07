<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class SafetradeTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'safetrade_transactions';

    protected $fillable = [
        'reference',
        'order_id',
        'buyer_id',
        'seller_id',
        'vehicle_id',
        'vehicle_title',
        'vehicle_price',
        'payment_method',
        'payment_status',
        'amount',
        'escrow_fee',
        'status',
        'escrow_status',
        'delivery_method',
        'delivery_address',
        'tracking_number',
        'payment_proof_path',
        'notes',
        'cancellation_reason',
        'funded_at',
        'confirmed_at',
        'delivered_at',
        'completed_at',
        'cancelled_at',
    ];

    protected $casts = [
        'vehicle_price' => 'decimal:2',
        'amount' => 'decimal:2',
        'escrow_fee' => 'decimal:2',
        'funded_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'delivered_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($txn) {
            if (empty($txn->reference)) {
                $txn->reference = 'AS24-ST-' . strtoupper(base_convert(time(), 10, 36)) . '-' . strtoupper(Str::random(4));
            }
        });
    }

    // Relationships
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

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

    public function escrow(): HasOne
    {
        return $this->hasOne(EscrowTransaction::class, 'safetrade_transaction_id');
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class, 'safetrade_transaction_id');
    }

    public function timeline(): HasMany
    {
        return $this->hasMany(TransactionTimeline::class, 'safetrade_transaction_id')
            ->orderBy('timestamp', 'desc');
    }

    public function disputes(): HasMany
    {
        return $this->hasMany(Dispute::class, 'transaction_id');
    }

    // ──── Workflow methods ────

    public function fund(string $paymentMethod = 'bank_transfer'): bool
    {
        if ($this->escrow_status !== 'pending') return false;

        $this->update([
            'payment_method' => $paymentMethod,
            'payment_status' => 'processing',
            'escrow_status' => 'funded',
            'status' => 'confirmed',
            'funded_at' => now(),
            'confirmed_at' => now(),
        ]);

        // Update escrow record
        if ($escrow = $this->escrow) {
            $escrow->update([
                'status' => 'funded',
                'funded_at' => now(),
            ]);
        }

        return true;
    }

    public function confirmReceipt(): bool
    {
        if (!in_array($this->escrow_status, ['funded'])) return false;

        $this->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);

        // Update release conditions
        if ($escrow = $this->escrow) {
            $conditions = $escrow->release_conditions ?? [];
            $conditions['buyer_confirmed'] = true;
            $escrow->update(['release_conditions' => $conditions]);
        }

        // Auto-release if conditions met
        $this->tryReleaseFunds();

        return true;
    }

    public function releaseFunds(): bool
    {
        if ($this->escrow_status !== 'funded') return false;

        // Prevent release while disputes are open
        if ($this->disputes()->whereNotIn('status', ['resolved', 'closed'])->exists()) {
            return false;
        }

        $this->update([
            'escrow_status' => 'released',
            'payment_status' => 'completed',
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        if ($escrow = $this->escrow) {
            $escrow->update([
                'status' => 'released',
                'released_at' => now(),
            ]);
        }

        // Complete the parent order
        if ($this->order) {
            $this->order->complete();
        }

        return true;
    }

    public function dispute(string $reason, array $evidence = []): bool
    {
        if (!in_array($this->escrow_status, ['funded'])) return false;

        $this->update([
            'status' => 'disputed',
            'escrow_status' => 'disputed',
        ]);

        if ($escrow = $this->escrow) {
            $escrow->update([
                'status' => 'disputed',
                'dispute_reason' => $reason,
                'dispute_evidence' => $evidence,
            ]);
        }

        return true;
    }

    public function refund(): bool
    {
        if (!in_array($this->escrow_status, ['funded', 'disputed'])) return false;

        $this->update([
            'escrow_status' => 'refunded',
            'payment_status' => 'refunded',
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        if ($escrow = $this->escrow) {
            $escrow->update([
                'status' => 'refunded',
                'refunded_at' => now(),
            ]);
        }

        return true;
    }

    public function cancelTransaction(string $reason): bool
    {
        if (in_array($this->status, ['completed', 'cancelled'])) return false;

        // If funded, refund first
        if ($this->escrow_status === 'funded') {
            $this->refund();
        }

        $this->update([
            'status' => 'cancelled',
            'cancellation_reason' => $reason,
            'cancelled_at' => now(),
        ]);

        if ($this->order) {
            $this->order->cancel();
        }

        return true;
    }

    // ──── Helpers ────

    private function tryReleaseFunds(): void
    {
        if ($this->status === 'delivered' && $this->escrow_status === 'funded') {
            $this->releaseFunds();
        }
    }

    public function addTimelineEvent(string $event, string $description, int $actorId, string $actorName, string $role = 'buyer', array $metadata = []): TransactionTimeline
    {
        return $this->timeline()->create([
            'event' => $event,
            'description' => $description,
            'actor_id' => $actorId,
            'actor_name' => $actorName,
            'actor_role' => $role,
            'metadata' => $metadata,
            'timestamp' => now(),
        ]);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['completed', 'cancelled']);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('buyer_id', $userId)->orWhere('seller_id', $userId);
        });
    }

    // Accessors
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Awaiting Payment',
            'payment_uploaded' => 'Payment Proof Uploaded',
            'funded' => 'Escrow Funded',
            'confirmed' => 'Payment Confirmed',
            'in_transit' => 'Vehicle In Transit',
            'delivered' => 'Vehicle Delivered',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'disputed' => 'Under Dispute',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }
}
