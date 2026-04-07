<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'channel',
        'payment_received',
        'payment_verified',
        'transaction_update',
        'message_received',
        'dispute_update',
        'price_alert',
        'new_listing_match',
        'pickup_reminder',
        'marketing',
        'weekly_digest',
    ];

    protected $casts = [
        'payment_received' => 'boolean',
        'payment_verified' => 'boolean',
        'transaction_update' => 'boolean',
        'message_received' => 'boolean',
        'dispute_update' => 'boolean',
        'price_alert' => 'boolean',
        'new_listing_match' => 'boolean',
        'pickup_reminder' => 'boolean',
        'marketing' => 'boolean',
        'weekly_digest' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function getDefaultPreferences(): array
    {
        return [
            'payment_received' => true,
            'payment_verified' => true,
            'transaction_update' => true,
            'message_received' => true,
            'dispute_update' => true,
            'price_alert' => true,
            'new_listing_match' => true,
            'pickup_reminder' => true,
            'marketing' => false,
            'weekly_digest' => true,
        ];
    }
}
