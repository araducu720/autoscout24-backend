<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionTimeline extends Model
{
    use HasFactory;

    protected $fillable = [
        'safetrade_transaction_id',
        'event',
        'description',
        'actor_id',
        'actor_name',
        'actor_role',
        'metadata',
        'timestamp',
    ];

    protected $casts = [
        'metadata' => 'array',
        'timestamp' => 'datetime',
    ];

    // Relationships
    public function safetradeTransaction(): BelongsTo
    {
        return $this->belongsTo(SafetradeTransaction::class, 'safetrade_transaction_id');
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
