<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Dealer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_name',
        'slug',
        'logo',
        'description',
        'address',
        'city',
        'postal_code',
        'country',
        'phone',
        'email',
        'website',
        'tax_id',
        'registration_number',
        'latitude',
        'longitude',
        'type',
        'is_verified',
        'is_active',
        'offers_home_delivery',
        'offers_financing',
        'offers_warranty',
        'rating',
        'total_reviews',
        'total_purchases',
        'verified_at',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'is_active' => 'boolean',
        'offers_home_delivery' => 'boolean',
        'offers_financing' => 'boolean',
        'offers_warranty' => 'boolean',
        'rating' => 'decimal:1',
        'verified_at' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'total_reviews' => 'integer',
        'total_purchases' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($dealer) {
            if (empty($dealer->slug)) {
                $dealer->slug = Str::slug($dealer->company_name) . '-' . Str::random(6);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get vehicles through the user relationship.
     */
    public function vehicles()
    {
        return $this->hasManyThrough(Vehicle::class, User::class, 'id', 'user_id', 'user_id', 'id');
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
