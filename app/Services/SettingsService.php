<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    /**
     * Get a single setting value.
     * Usage: $service->get('general.site_name', 'AutoScout24')
     */
    public function get(string $key, mixed $default = null): mixed
    {
        try {
            return Setting::getValue($key, $default);
        } catch (\Illuminate\Database\QueryException $e) {
            // Settings table may not exist yet (migrations not run)
            \Log::warning("SettingsService: Could not read setting '{$key}', using default. Error: " . $e->getMessage());
            return $default;
        }
    }

    /**
     * Set a single setting value.
     * Usage: $service->set('general.site_name', 'AutoScout24 Clone')
     */
    public function set(string $key, mixed $value): void
    {
        Setting::setValue($key, $value);
    }

    /**
     * Get all settings in a group.
     */
    public function group(string $group): array
    {
        return Setting::getGroup($group);
    }

    /**
     * Get all public settings (safe for frontend).
     */
    public function publicSettings(): array
    {
        return Setting::getPublicSettings();
    }

    /**
     * Bulk update settings.
     * $data = ['group.key' => 'value', ...]
     */
    public function bulkUpdate(array $data): int
    {
        $updated = 0;

        foreach ($data as $key => $value) {
            Setting::setValue($key, $value);
            $updated++;
        }

        return $updated;
    }

    /**
     * Clear all settings caches.
     */
    public function clearCache(): void
    {
        Setting::clearCache();
    }

    // ──── Convenience accessors for common settings ────

    public function siteName(): string
    {
        return $this->get('general.site_name', 'AutoScout24');
    }

    public function siteDescription(): string
    {
        return $this->get('general.site_description', 'European Vehicle Marketplace');
    }

    public function contactEmail(): string
    {
        return $this->get('general.contact_email', 'info@autoscout24.com');
    }

    public function defaultCurrency(): string
    {
        return $this->get('general.default_currency', 'EUR');
    }

    public function defaultLocale(): string
    {
        return $this->get('general.default_locale', 'en');
    }

    public function maintenanceMode(): bool
    {
        return $this->get('general.maintenance_mode', false);
    }

    public function safetradeEnabled(): bool
    {
        return $this->get('safetrade.enabled', true);
    }

    public function escrowFeePercent(): float
    {
        return $this->get('safetrade.escrow_fee_percent', 2.5);
    }

    public function maxVehicleImages(): int
    {
        return $this->get('listings.max_vehicle_images', 20);
    }

    public function listingsPerPage(): int
    {
        return $this->get('listings.per_page', 20);
    }

    public function registrationEnabled(): bool
    {
        return $this->get('general.registration_enabled', true);
    }

    public function googleAnalyticsId(): ?string
    {
        return $this->get('seo.google_analytics_id');
    }
}
