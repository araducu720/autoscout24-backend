<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'group',
        'key',
        'value',
        'type',
        'label',
        'description',
        'options',
        'is_public',
        'sort_order',
    ];

    protected $casts = [
        'options' => 'array',
        'is_public' => 'boolean',
        'sort_order' => 'integer',
    ];

    // ──── Scopes ────

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeGroup($query, string $group)
    {
        return $query->where('group', $group);
    }

    // ──── Accessors ────

    /**
     * Get the typed value.
     */
    public function getTypedValueAttribute(): mixed
    {
        return self::castValue($this->value, $this->type);
    }

    /**
     * Cast a raw string value to the correct PHP type.
     */
    public static function castValue(?string $value, string $type): mixed
    {
        if ($value === null) {
            return null;
        }

        return match ($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $value,
            'float'   => (float) $value,
            'json'    => json_decode($value, true),
            default   => $value,
        };
    }

    // ──── Static Helpers ────

    /**
     * Get a setting value by group.key with optional default.
     */
    public static function getValue(string $key, mixed $default = null): mixed
    {
        $parts = explode('.', $key, 2);

        if (count($parts) !== 2) {
            return $default;
        }

        [$group, $settingKey] = $parts;

        $cacheKey = "settings.{$group}.{$settingKey}";

        return Cache::remember($cacheKey, 3600, function () use ($group, $settingKey, $default) {
            $setting = self::where('group', $group)->where('key', $settingKey)->first();

            if (!$setting) {
                return $default;
            }

            return $setting->typed_value;
        });
    }

    /**
     * Set a setting value.
     */
    public static function setValue(string $key, mixed $value): void
    {
        $parts = explode('.', $key, 2);

        if (count($parts) !== 2) {
            return;
        }

        [$group, $settingKey] = $parts;

        $setting = self::where('group', $group)->where('key', $settingKey)->first();

        if ($setting) {
            if ($setting->type === 'json' && is_array($value)) {
                $value = json_encode($value);
            } elseif ($setting->type === 'boolean') {
                $value = $value ? '1' : '0';
            } else {
                $value = (string) $value;
            }

            $setting->update(['value' => $value]);
            Cache::forget("settings.{$group}.{$settingKey}");
            Cache::forget("settings.public");
            Cache::forget("settings.group.{$group}");
        }
    }

    /**
     * Get all settings for a group as key => typed_value array.
     */
    public static function getGroup(string $group): array
    {
        return Cache::remember("settings.group.{$group}", 3600, function () use ($group) {
            return self::where('group', $group)
                ->orderBy('sort_order')
                ->get()
                ->mapWithKeys(fn ($s) => [$s->key => $s->typed_value])
                ->toArray();
        });
    }

    /**
     * Get all public settings grouped.
     */
    public static function getPublicSettings(): array
    {
        return Cache::remember('settings.public', 3600, function () {
            return self::where('is_public', true)
                ->orderBy('group')
                ->orderBy('sort_order')
                ->get()
                ->groupBy('group')
                ->map(fn ($items) => $items->mapWithKeys(fn ($s) => [$s->key => $s->typed_value]))
                ->toArray();
        });
    }

    /**
     * Clear all settings cache.
     */
    public static function clearCache(): void
    {
        $groups = self::distinct()->pluck('group');

        foreach ($groups as $group) {
            Cache::forget("settings.group.{$group}");

            $keys = self::where('group', $group)->pluck('key');
            foreach ($keys as $key) {
                Cache::forget("settings.{$group}.{$key}");
            }
        }

        Cache::forget('settings.public');
    }
}
