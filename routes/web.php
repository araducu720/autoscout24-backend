<?php
// Route cache buster v2 — forces ensureFreshRouteCache() to detect hash change

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

Route::get('/', function () {
    return view('welcome');
});

// Named route for password reset — redirects to frontend
Route::get('/reset-password/{token}', function (string $token) {
    $email = request()->query('email', '');
    $frontendUrl = rtrim(config('app.frontend_url', 'https://www.autoscout24safetrade.com'), '/');
    return redirect($frontendUrl . '/reset-password?token=' . $token . '&email=' . urlencode($email));
})->name('password.reset');

// Emergency cache clear endpoint (accessible even with stale route cache since it's a web route)
Route::get('/clear-cache/{key}', function (string $key) {
    if ($key !== config('app.cache_clear_key', env('CACHE_CLEAR_KEY', 'AS24_x7K9mP2qR5'))) {
        abort(403);
    }
    try {
        Artisan::call('route:clear');
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        // Also delete stale hash file
        $hashFile = storage_path('framework/route-hash.txt');
        if (file_exists($hashFile)) @unlink($hashFile);
        return response()->json(['status' => 'ok', 'message' => 'All caches cleared']);
    } catch (\Exception $e) {
        return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
    }
});