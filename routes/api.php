<?php
// Route cache buster v3 — forces ensureFreshRouteCache() to detect hash change

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ConversationController;
use App\Http\Controllers\Api\ContactMessageController;
use App\Http\Controllers\Api\DisputeController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\FinanceController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PasswordResetController;
use App\Http\Controllers\Api\PhoneRevealController;
use App\Http\Controllers\Api\PriceAlertController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\SavedSearchController;
use App\Http\Controllers\Api\SellerController;
use App\Http\Controllers\Api\SettingsController;
use App\Http\Controllers\Api\TestDriveController;
use App\Http\Controllers\Api\VehicleController;
use App\Http\Controllers\Api\VehicleMakeController;
use App\Http\Controllers\Api\VehicleModelController;
use App\Http\Controllers\Api\SmyleController;
use App\Http\Controllers\Api\TwoFactorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public routes
Route::prefix('v1')->group(function () {
    // Auth routes (with rate limiting for security)
    Route::middleware('throttle:auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
    });

    // Password Reset (with strict rate limiting)
    Route::middleware('throttle:password-reset')->group(function () {
        Route::post('/forgot-password', [PasswordResetController::class, 'forgotPassword']);
        Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);
    });

    // Email verification (public endpoint with signed URL)
    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
        ->name('verification.verify');

    // Vehicle routes (public)
    Route::get('/vehicles/stats', [VehicleController::class, 'stats']);
    Route::get('/vehicles', [VehicleController::class, 'index']);
    Route::get('/vehicles/{id}', [VehicleController::class, 'show']);
    Route::get('/vehicles/{id}/similar', [VehicleController::class, 'similar']);

    // Vehicle valuation (public — no auth needed for instant quote)
    Route::post('/vehicles/valuation', [VehicleController::class, 'valuation'])
        ->middleware('throttle:20,1');

    // Vehicle Makes routes
    Route::get('/makes', [VehicleMakeController::class, 'index']);
    Route::get('/makes/{id}', [VehicleMakeController::class, 'show']);
    Route::get('/makes/{makeId}/models', [VehicleMakeController::class, 'models']);

    // Vehicle Models routes
    Route::get('/models', [VehicleModelController::class, 'index']);
    Route::get('/models/{id}', [VehicleModelController::class, 'show']);

    // Public dealers listing
    Route::get('/dealers', [\App\Http\Controllers\Api\PublicDealerController::class, 'index']);
    Route::get('/dealers/{id}/profile', [\App\Http\Controllers\Api\PublicDealerController::class, 'show']);
    Route::get('/dealers/{id}/vehicles', [\App\Http\Controllers\Api\PublicDealerController::class, 'vehicles']);
    Route::get('/dealers/{id}/ratings', [\App\Http\Controllers\Api\PublicDealerController::class, 'ratings']);

    // Public seller profiles
    Route::get('/sellers/{sellerId}/profile', [SellerController::class, 'profile']);
    Route::get('/sellers/{sellerId}/vehicles', [SellerController::class, 'vehicles']);
    Route::get('/sellers/{sellerId}/ratings', [SellerController::class, 'ratings']);

    // Public vehicle reviews
    Route::get('/vehicles/{vehicleId}/reviews', [ReviewController::class, 'index']);

    // Contact messages (with rate limiting)
    Route::middleware('throttle:contact')->group(function () {
        Route::post('/contact-messages', [ContactMessageController::class, 'store']);
    });

    // Phone reveal (track when users view phone numbers)
    Route::post('/vehicles/{vehicleId}/reveal-phone', [PhoneRevealController::class, 'reveal'])
        ->middleware('throttle:20,1');

    // Test drive requests
    Route::post('/test-drives', [TestDriveController::class, 'store'])
        ->middleware('throttle:10,1'); // Max 10 requests per minute

    // Health check endpoint
    Route::get('/health', function () {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now()->toISOString(),
            'version' => config('app.version', '1.0.0'),
        ]);
    });

    // ===========================================
    // BULK IMPORT (Protected by secret key + rate limited)
    // ===========================================
    Route::middleware('throttle:30,1')->group(function () {
        Route::post('/import/vehicle', [\App\Http\Controllers\Api\BulkImportController::class, 'importVehicle']);
        Route::get('/import/status', [\App\Http\Controllers\Api\BulkImportController::class, 'status']);
    });

    // ===========================================
    // APPLICATION SETTINGS (Public)
    // ===========================================
    Route::get('/settings', [SettingsController::class, 'publicSettings']);
    Route::get('/settings/{group}', [SettingsController::class, 'group']);

    // ===========================================
    // FINANCE & CURRENCY (Public)
    // ===========================================
    Route::prefix('finance')->middleware('throttle:30,1')->group(function () {
        Route::post('/calculate', [FinanceController::class, 'calculate']);
        Route::post('/compare-options', [FinanceController::class, 'compareOptions']);
        Route::get('/currencies', [FinanceController::class, 'currencies']);
        Route::get('/exchange-rates', [FinanceController::class, 'exchangeRates']);
        Route::post('/convert', [FinanceController::class, 'convert']);
    });

    // ===========================================
    // SMYLE - Online Car Buying (Public)
    // ===========================================
    Route::prefix('smyle')->group(function () {
        Route::get('/vehicles', [SmyleController::class, 'vehicles']);
        Route::get('/vehicles/{id}', [SmyleController::class, 'vehicleShow']);
        Route::post('/delivery-cost', [SmyleController::class, 'deliveryCost']);
        Route::get('/eligibility/{vehicleId}', [SmyleController::class, 'checkEligibility']);
        Route::post('/financing/calculate', [SmyleController::class, 'calculateFinancing']);
        Route::get('/stats', [SmyleController::class, 'stats']);
    });
});

// Protected routes
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Email verification
    Route::post('/email/verification-notification', [AuthController::class, 'resendVerificationEmail'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    // Change password
    Route::post('/change-password', [PasswordResetController::class, 'changePassword']);

    // Two-Factor Authentication
    Route::prefix('two-factor')->middleware('throttle:6,1')->group(function () {
        Route::post('/setup', [TwoFactorController::class, 'setup']);
        Route::post('/confirm', [TwoFactorController::class, 'confirm']);
        Route::post('/verify', [TwoFactorController::class, 'verify']);
        Route::delete('/disable', [TwoFactorController::class, 'disable']);
        Route::post('/recovery-codes', [TwoFactorController::class, 'regenerateRecoveryCodes']);
    });

    // Vehicle CRUD (authenticated — owner operations)
    Route::post('/vehicles', [VehicleController::class, 'store']);
    Route::put('/vehicles/{id}', [VehicleController::class, 'update']);
    Route::delete('/vehicles/{id}', [VehicleController::class, 'destroyListing']);
    Route::post('/vehicles/{id}/submit', [VehicleController::class, 'submit']);
    Route::post('/vehicles/{id}/images', [VehicleController::class, 'uploadImages']);

    // Favorites
    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::post('/favorites', [FavoriteController::class, 'store']);
    Route::delete('/favorites/{vehicleId}', [FavoriteController::class, 'destroy']);

    // Test drive requests (user's own requests)
    Route::get('/test-drives', [TestDriveController::class, 'index']);

    // Saved searches
    Route::get('/saved-searches', [SavedSearchController::class, 'index']);
    Route::post('/saved-searches', [SavedSearchController::class, 'store']);
    Route::get('/saved-searches/{id}', [SavedSearchController::class, 'show']);
    Route::put('/saved-searches/{id}', [SavedSearchController::class, 'update']);
    Route::delete('/saved-searches/{id}', [SavedSearchController::class, 'destroy']);

    // ===========================================
    // USER PROFILE
    // ===========================================
    Route::prefix('profile')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\ProfileController::class, 'show']);
        Route::put('/', [\App\Http\Controllers\Api\ProfileController::class, 'update']);
        Route::put('/bank-details', [\App\Http\Controllers\Api\ProfileController::class, 'updateBankDetails']);
        Route::get('/bank-details', [\App\Http\Controllers\Api\ProfileController::class, 'getBankDetails']);
        Route::put('/preferences', [\App\Http\Controllers\Api\ProfileController::class, 'updatePreferences']);
        Route::post('/change-password', [\App\Http\Controllers\Api\ProfileController::class, 'changePassword']);
    });

    // ===========================================
    // DEALER ROUTES
    // ===========================================
    Route::prefix('dealer')->group(function () {
        // Dealer profile
        Route::get('/profile', [\App\Http\Controllers\Api\DealerController::class, 'profile']);
        Route::post('/register', [\App\Http\Controllers\Api\DealerController::class, 'register']);
        Route::put('/profile', [\App\Http\Controllers\Api\DealerController::class, 'update']);
        Route::get('/statistics', [\App\Http\Controllers\Api\DealerController::class, 'statistics']);
    });

    // ===========================================
    // SAFETRADE TRANSACTIONS (Vehicle Purchase Flow)
    // ===========================================
    Route::prefix('transactions')->middleware('throttle:60,1')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\SafetradeController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\Api\SafetradeController::class, 'store']);
        Route::get('/{id}', [\App\Http\Controllers\Api\SafetradeController::class, 'show']);
        Route::get('/{id}/details', [\App\Http\Controllers\Api\SafetradeController::class, 'details']);
        Route::put('/{id}/status', [\App\Http\Controllers\Api\SafetradeController::class, 'updateStatus']);
        Route::put('/{id}/tracking', [\App\Http\Controllers\Api\SafetradeController::class, 'updateTracking']);
        Route::post('/{id}/confirm-delivery', [\App\Http\Controllers\Api\SafetradeController::class, 'confirmDelivery']);
        Route::post('/{id}/complete', [\App\Http\Controllers\Api\SafetradeController::class, 'complete']);
        Route::post('/{id}/cancel', [\App\Http\Controllers\Api\SafetradeController::class, 'cancel']);
    });

    // ===========================================
    // ESCROW (SafeTrade Payment Protection)
    // ===========================================
    Route::prefix('escrow')->middleware('throttle:30,1')->group(function () {
        Route::get('/transaction/{transactionId}', [\App\Http\Controllers\Api\EscrowController::class, 'show']);
        Route::post('/{transactionId}/fund', [\App\Http\Controllers\Api\EscrowController::class, 'fund']);
        Route::post('/{transactionId}/confirm-receipt', [\App\Http\Controllers\Api\EscrowController::class, 'confirmReceipt']);
        Route::post('/{transactionId}/release', [\App\Http\Controllers\Api\EscrowController::class, 'release']);
        Route::post('/{transactionId}/dispute', [\App\Http\Controllers\Api\EscrowController::class, 'dispute']);
        Route::post('/{escrowId}/resolve', [\App\Http\Controllers\Api\EscrowController::class, 'resolve']);
    });

    // ===========================================
    // ORDERS (Direct Purchase)
    // ===========================================
    Route::prefix('orders')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\OrderController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\Api\OrderController::class, 'store']);
        Route::get('/{id}', [\App\Http\Controllers\Api\OrderController::class, 'show']);
        Route::post('/{id}/accept', [\App\Http\Controllers\Api\OrderController::class, 'accept']);
        Route::post('/{id}/reject', [\App\Http\Controllers\Api\OrderController::class, 'reject']);
        Route::post('/{id}/cancel', [\App\Http\Controllers\Api\OrderController::class, 'cancel']);
    });



    // ===========================================
    // NOTIFICATIONS (Real-time ready)
    // ===========================================
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/unread-count', [NotificationController::class, 'unreadCount']);
        Route::put('/{notification}/read', [NotificationController::class, 'markAsRead']);
        Route::put('/read-all', [NotificationController::class, 'markAllRead']);
        Route::delete('/{notification}', [NotificationController::class, 'destroy']);
        Route::get('/preferences', [NotificationController::class, 'getPreferences']);
        Route::put('/preferences', [NotificationController::class, 'updatePreferences']);
    });

    // ===========================================
    // INVOICES
    // ===========================================
    Route::prefix('invoices')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\InvoiceController::class, 'index']);
        Route::get('/{id}', [\App\Http\Controllers\Api\InvoiceController::class, 'show']);
        Route::get('/{id}/pdf', [\App\Http\Controllers\Api\InvoiceController::class, 'generatePDF']);
        Route::post('/{id}/send', [\App\Http\Controllers\Api\InvoiceController::class, 'send']);
    });

    // ===========================================
    // CONVERSATIONS / MESSAGING
    // ===========================================
    Route::prefix('conversations')->group(function () {
        Route::get('/', [ConversationController::class, 'index']);
        Route::post('/', [ConversationController::class, 'store']);
        Route::get('/{id}', [ConversationController::class, 'show']);
        Route::get('/{id}/messages', [ConversationController::class, 'messages']);
        Route::post('/{id}/messages', [ConversationController::class, 'sendMessage']);
        Route::put('/{id}/read', [ConversationController::class, 'markRead']);
        Route::delete('/{id}', [ConversationController::class, 'destroy']);
        Route::get('/{id}/typing', [ConversationController::class, 'getTyping']);
        Route::post('/{id}/typing', [ConversationController::class, 'setTyping']);
    });

    // ===========================================
    // DASHBOARD
    // ===========================================
    Route::prefix('dashboard')->group(function () {
        Route::get('/stats', [\App\Http\Controllers\Api\DashboardController::class, 'stats']);
        Route::get('/activity', [\App\Http\Controllers\Api\DashboardController::class, 'activity']);
    });

    // ===========================================
    // MY VEHICLES (authenticated user's own listings)
    // ===========================================
    Route::get('/my-vehicles', [\App\Http\Controllers\Api\SellerListingController::class, 'myVehicles']);
    Route::get('/my-vehicles/stats', [\App\Http\Controllers\Api\SellerListingController::class, 'myVehicleStats']);

    // ===========================================
    // SELLER LISTINGS & ANALYTICS
    // ===========================================
    Route::get('/sellers/{sellerId}/listings', [\App\Http\Controllers\Api\SellerListingController::class, 'index']);
    Route::get('/sellers/{sellerId}/inventory-stats', [\App\Http\Controllers\Api\SellerListingController::class, 'inventoryStats']);
    Route::get('/vehicles/{vehicleId}/analytics', [\App\Http\Controllers\Api\SellerListingController::class, 'analytics']);
    Route::post('/listings/bulk-action', [\App\Http\Controllers\Api\SellerListingController::class, 'bulkAction']);
    Route::post('/listings/reorder', [\App\Http\Controllers\Api\SellerListingController::class, 'reorder']);
    Route::post('/vehicles/{vehicleId}/mark-sold', [\App\Http\Controllers\Api\SellerListingController::class, 'markSold']);
    Route::post('/vehicles/{vehicleId}/promote-featured', [\App\Http\Controllers\Api\SellerListingController::class, 'promoteFeatured']);
    Route::post('/vehicles/{vehicleId}/renew', [\App\Http\Controllers\Api\SellerListingController::class, 'renew']);

    // ===========================================
    // FAVORITE LISTS
    // ===========================================
    Route::prefix('favorite-lists')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\FavoriteListController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\Api\FavoriteListController::class, 'store']);
        Route::post('/{listId}/items', [\App\Http\Controllers\Api\FavoriteListController::class, 'addItem']);
        Route::delete('/{listId}/items/{vehicleId}', [\App\Http\Controllers\Api\FavoriteListController::class, 'removeItem']);
    });

    // ===========================================
    // PRICE ALERTS
    // ===========================================
    Route::prefix('price-alerts')->group(function () {
        Route::get('/', [PriceAlertController::class, 'index']);
        Route::post('/', [PriceAlertController::class, 'store']);
        Route::put('/{alert}', [PriceAlertController::class, 'update']);
        Route::delete('/{alert}', [PriceAlertController::class, 'destroy']);
    });

    // ===========================================
    // DISPUTES
    // ===========================================
    Route::prefix('disputes')->group(function () {
        Route::get('/', [DisputeController::class, 'index']);
        Route::post('/', [DisputeController::class, 'store']);
        Route::get('/{dispute}', [DisputeController::class, 'show']);
        Route::post('/{dispute}/evidence', [DisputeController::class, 'addEvidence']);
        Route::get('/{dispute}/timeline', [DisputeController::class, 'timeline']);
        Route::post('/{dispute}/propose-resolution', [DisputeController::class, 'proposeResolution']);
        Route::post('/{dispute}/accept-resolution', [DisputeController::class, 'acceptResolution']);
        Route::post('/{dispute}/reject-resolution', [DisputeController::class, 'rejectResolution']);
        Route::post('/{dispute}/close', [DisputeController::class, 'close']);
    });

    // ===========================================
    // REVIEWS (Protected writes + user review queries)
    // ===========================================
    Route::get('/user/reviews', [ReviewController::class, 'userReviews']);
    Route::get('/user/received-reviews', [ReviewController::class, 'receivedReviews']);
    Route::post('/vehicles/{vehicleId}/reviews', [ReviewController::class, 'store']);
    Route::put('/reviews/{reviewId}', [ReviewController::class, 'update']);
    Route::delete('/reviews/{reviewId}', [ReviewController::class, 'destroy']);
    Route::post('/reviews/{reviewId}/helpful', [ReviewController::class, 'markHelpful']);

    // ===========================================
    // ADMIN SETTINGS (requires admin role)
    // ===========================================
    Route::prefix('admin/settings')->middleware('throttle:30,1')->group(function () {
        Route::get('/', [SettingsController::class, 'adminIndex']);
        Route::put('/', [SettingsController::class, 'adminUpdate']);
        Route::post('/clear-cache', [SettingsController::class, 'clearCache']);
    });

    // ===========================================
    // SMYLE ORDERS (Authenticated - Online Car Purchase)
    // ===========================================
    Route::prefix('smyle')->middleware('throttle:60,1')->group(function () {
        Route::get('/orders', [SmyleController::class, 'orders']);
        Route::post('/orders', [SmyleController::class, 'createOrder']);
        Route::get('/orders/{id}', [SmyleController::class, 'orderShow']);
        Route::post('/orders/{id}/deposit', [SmyleController::class, 'payDeposit']);
        Route::post('/orders/{id}/cancel', [SmyleController::class, 'cancelOrder']);
        Route::post('/orders/{id}/return', [SmyleController::class, 'returnOrder']);
        Route::put('/orders/{id}/status', [SmyleController::class, 'updateOrderStatus']);
        Route::get('/orders/{id}/timeline', [SmyleController::class, 'orderTimeline']);
        Route::post('/orders/{orderId}/financing', [SmyleController::class, 'applyFinancing']);
    });
});
