<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use App\Notifications\PasswordResetNotification;
use App\Notifications\EmailVerificationNotification;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements MustVerifyEmail, FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        // Bank details
        'bank_name',
        'iban',
        'bic',
        'account_holder',
        // Preferences
        'locale',
        'currency',
        'country',
        // Verification
        'bank_details_verified',
        'bank_details_verified_at',
        // Admin
        'is_admin',
        'email_verified_at',
        // Two-factor auth
        'two_factor_secret',
        'two_factor_enabled',
        'two_factor_recovery_codes',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'iban',
        'bic',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'iban' => 'encrypted',
            'bic' => 'encrypted',
            'bank_details_verified' => 'boolean',
            'bank_details_verified_at' => 'datetime',
            'is_admin' => 'boolean',
            'identity_verified' => 'boolean',
            'identity_verified_at' => 'datetime',
            'two_factor_enabled' => 'boolean',
            'two_factor_secret' => 'encrypted',
            'two_factor_recovery_codes' => 'encrypted:array',
            'two_factor_confirmed_at' => 'datetime',
            'trust_score' => 'integer',
        ];
    }

    /**
     * Check if user has complete bank details.
     */
    /**
     * Block mail notifications to imported/fake email domains to prevent hard bounces.
     */
    public function routeNotificationForMail($notification = null): ?string
    {
        $blockedDomains = ['autoscout-import.local', 'autoscout24.com', 'test.com', 'test-autoscout.dev'];
        $domain = substr(strrchr($this->email, '@'), 1);

        if (in_array($domain, $blockedDomains, true)) {
            return null;
        }

        return $this->email;
    }

    /**
     * Determine if the user can access the Filament admin panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return (bool) $this->is_admin;
    }

    /**
     * Send the password reset notification with branded template.
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new PasswordResetNotification($token));
    }

    /**
     * Send the email verification notification with branded template.
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new EmailVerificationNotification());
    }

    public function hasBankDetails(): bool
    {
        return !empty($this->bank_name) 
            && !empty($this->iban) 
            && !empty($this->account_holder);
    }

    /**
     * Get bank details as array.
     */
    public function getBankDetailsAttribute(): array
    {
        return [
            'bank_name' => $this->bank_name,
            'iban' => $this->iban,
            'bic' => $this->bic,
            'account_holder' => $this->account_holder,
            'verified' => $this->bank_details_verified,
        ];
    }

    /**
     * Get the dealer profile if exists.
     */
    public function dealer(): HasOne
    {
        return $this->hasOne(Dealer::class);
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function favoriteVehicles(): BelongsToMany
    {
        return $this->belongsToMany(Vehicle::class, 'favorites');
    }

    public function priceAlerts(): HasMany
    {
        return $this->hasMany(PriceAlert::class);
    }

    public function notificationPreferences(): HasMany
    {
        return $this->hasMany(NotificationPreference::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'buyer_id');
    }

    public function sellerOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'seller_id');
    }

    public function safetradeTransactions(): HasMany
    {
        return $this->hasMany(SafetradeTransaction::class, 'buyer_id');
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'buyer_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function savedSearches(): HasMany
    {
        return $this->hasMany(SavedSearch::class);
    }

    public function testDriveRequests(): HasMany
    {
        return $this->hasMany(TestDriveRequest::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function getNotificationPreference(string $channel = 'email'): NotificationPreference
    {
        return $this->notificationPreferences()
            ->firstOrCreate(
                ['channel' => $channel],
                NotificationPreference::getDefaultPreferences()
            );
    }

    public function shouldNotify(string $type, string $channel = 'email'): bool
    {
        $prefs = $this->getNotificationPreference($channel);
        return $prefs->{$type} ?? true;
    }
}
