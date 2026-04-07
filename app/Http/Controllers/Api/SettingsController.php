<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class SettingsController extends Controller
{
    /**
     * Get all public settings (no auth required).
     * Used by the frontend to configure the app.
     */
    public function publicSettings(): JsonResponse
    {
        $settings = Setting::getPublicSettings();

        return response()->json([
            'success' => true,
            'data' => $settings,
        ]);
    }

    /**
     * Get settings for a specific group (public only).
     */
    public function group(string $group): JsonResponse
    {
        $settings = Setting::where('group', $group)
            ->where('is_public', true)
            ->orderBy('sort_order')
            ->get()
            ->mapWithKeys(fn ($s) => [$s->key => $s->typed_value]);

        return response()->json([
            'success' => true,
            'data' => $settings,
        ]);
    }

    /**
     * Admin: Get ALL settings (including private).
     */
    public function adminIndex(Request $request): JsonResponse
    {
        abort_unless($request->user()?->is_admin, 403, 'Admin access required.');
        $settings = Setting::orderBy('group')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('group')
            ->map(fn ($items) => $items->map(fn ($s) => [
                'key' => $s->key,
                'value' => $s->typed_value,
                'type' => $s->type,
                'label' => $s->label,
                'description' => $s->description,
                'is_public' => $s->is_public,
            ]));

        return response()->json([
            'success' => true,
            'data' => $settings,
        ]);
    }

    /**
     * Admin: Update settings in bulk.
     */
    public function adminUpdate(Request $request): JsonResponse
    {
        abort_unless($request->user()?->is_admin, 403, 'Admin access required.');

        $request->validate([
            'settings' => 'required|array',
            'settings.*.key' => 'required|string',
            'settings.*.value' => 'nullable',
        ]);

        $updated = 0;

        foreach ($request->settings as $item) {
            $parts = explode('.', $item['key'], 2);

            if (count($parts) !== 2) {
                continue;
            }

            Setting::setValue($item['key'], $item['value']);
            $updated++;
        }

        return response()->json([
            'success' => true,
            'message' => "{$updated} settings updated.",
        ]);
    }

    /**
     * Admin: Clear ALL application caches (settings, routes, config, views, events).
     */
    public function clearCache(Request $request): JsonResponse
    {
        abort_unless($request->user()?->is_admin, 403, 'Admin access required.');

        $cleared = [];

        // Clear settings cache
        Setting::clearCache();
        $cleared[] = 'settings';

        // Clear route cache
        try {
            Artisan::call('route:clear');
            $cleared[] = 'routes';
        } catch (\Exception $e) {
            // ignore
        }

        // Clear config cache
        try {
            Artisan::call('config:clear');
            $cleared[] = 'config';
        } catch (\Exception $e) {
            // ignore
        }

        // Clear view cache
        try {
            Artisan::call('view:clear');
            $cleared[] = 'views';
        } catch (\Exception $e) {
            // ignore
        }

        // Clear general cache
        try {
            Artisan::call('cache:clear');
            $cleared[] = 'cache';
        } catch (\Exception $e) {
            // ignore
        }

        // Clear event cache
        try {
            Artisan::call('event:clear');
            $cleared[] = 'events';
        } catch (\Exception $e) {
            // ignore
        }

        return response()->json([
            'success' => true,
            'message' => 'All caches cleared: ' . implode(', ', $cleared),
            'cleared' => $cleared,
        ]);
    }
}
