<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EscrowTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'safetrade_transaction_id',
        'buyer_id',
        'seller_id',
        'amount',
        'status',
        'release_conditions',
        'dispute_reason',
        'dispute_evidence',
        'payment_reference',
        'escrow_iban',
        'funded_at',
        'released_at',
        'refunded_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'release_conditions' => 'array',
        'dispute_evidence' => 'array',
        'funded_at' => 'datetime',
        'released_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    // Relationships
    public function safetradeTransaction(): BelongsTo
    {
        return $this->belongsTo(SafetradeTransaction::class, 'safetrade_transaction_id');
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    // Scopes
    public function scopeFunded($query)
    {
        return $query->where('status', 'funded');
    }

    public function scopeDisputed($query)
    {
        return $query->where('status', 'disputed');
    }

    // Helpers
    public function isFunded(): bool
    {
        return $this->status === 'funded';
    }

    public function isDisputed(): bool
    {
        return $this->status === 'disputed';
    }

    public function isReleased(): bool
    {
        return $this->status === 'released';
    }
}
