<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmyleOrderTimeline extends Model
{
    protected $table = 'smyle_order_timeline';

    protected $fillable = [
        'smyle_order_id', 'event', 'description',
        'actor_id', 'actor_name', 'actor_role',
        'metadata', 'timestamp',
    ];

    protected $casts = [
        'metadata' => 'array',
        'timestamp' => 'datetime',
    ];

    public function smyleOrder(): BelongsTo
    {
        return $this->belongsTo(SmyleOrder::class);
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
