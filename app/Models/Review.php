<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'user_id',
        'transaction_id',
        'rating',
        'comment',
        'rating_vehicle',
        'rating_seller',
        'rating_shipping',
        'photos',
        'anonymous',
        'helpful_count',
        'status',
    ];

    protected $casts = [
        'rating' => 'integer',
        'rating_vehicle' => 'integer',
        'rating_seller' => 'integer',
        'rating_shipping' => 'integer',
        'photos' => 'array',
        'anonymous' => 'boolean',
        'helpful_count' => 'integer',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(SafetradeTransaction::class, 'transaction_id');
    }

    public function helpfulBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'review_helpful');
    }
}
