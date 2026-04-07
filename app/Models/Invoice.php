<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'safetrade_transaction_id',
        'invoice_number',
        'issue_date',
        'due_date',
        'amount',
        'escrow_fee',
        'total',
        'status',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'escrow_fee' => 'decimal:2',
        'total' => 'decimal:2',
        'issue_date' => 'date',
        'due_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            if (empty($invoice->invoice_number)) {
                $invoice->invoice_number = 'INV-' . date('Ymd') . '-' . str_pad(static::count() + 1, 5, '0', STR_PAD_LEFT);
            }
            if (empty($invoice->issue_date)) {
                $invoice->issue_date = now();
            }
        });
    }

    // Relationships
    public function safetradeTransaction(): BelongsTo
    {
        return $this->belongsTo(SafetradeTransaction::class, 'safetrade_transaction_id');
    }

    // Actions
    public function markPaid(): bool
    {
        return $this->update(['status' => 'paid']);
    }

    public function markOverdue(): bool
    {
        return $this->update(['status' => 'overdue']);
    }

    // Scopes
    public function scopeUnpaid($query)
    {
        return $query->whereIn('status', ['draft', 'issued', 'overdue']);
    }
}
