<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Shopify Webhook Route (No Auth - HMAC Verified)
|--------------------------------------------------------------------------
*/
Route::post('/shopify/webhook/{storeUuid}', [Modules\Shopify\Controllers\WebhookController::class, 'handle'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['auth:admin'])->group(function () {
    Route::post('/addons/setup/shopify', [Modules\Shopify\Controllers\SetupController::class, 'store']);
    Route::put('/addons/setup/shopify', [Modules\Shopify\Controllers\SetupController::class, 'update']);
});

/*
|--------------------------------------------------------------------------
| User Routes (Authenticated)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:user'])->group(function () {
    Route::prefix('/integrations/shopify')->group(function () {
        // Dashboard
        Route::get('/', [Modules\Shopify\Controllers\ShopifyController::class, 'index']);

        // OAuth flow
        Route::post('/oauth/redirect', [Modules\Shopify\Controllers\ShopifyController::class, 'oauthRedirect']);
        Route::get('/oauth/callback', [Modules\Shopify\Controllers\ShopifyController::class, 'oauthCallback']);

        // Store connections (manual)
        Route::post('/stores', [Modules\Shopify\Controllers\ShopifyController::class, 'storeConnection']);
        Route::put('/stores/{uuid}', [Modules\Shopify\Controllers\ShopifyController::class, 'updateConnection']);
        Route::delete('/stores/{uuid}', [Modules\Shopify\Controllers\ShopifyController::class, 'deleteConnection']);

        // Notification templates
        Route::get('/stores/{storeUuid}/notification-templates', [Modules\Shopify\Controllers\ShopifyController::class, 'getNotificationTemplates']);
        Route::post('/stores/{storeUuid}/notification-templates', [Modules\Shopify\Controllers\ShopifyController::class, 'saveNotificationTemplates']);

        // Cart recovery settings
        Route::get('/stores/{storeUuid}/cart-recovery', [Modules\Shopify\Controllers\ShopifyController::class, 'getCartRecoverySettings']);
        Route::post('/stores/{storeUuid}/cart-recovery', [Modules\Shopify\Controllers\ShopifyController::class, 'saveCartRecoverySettings']);

        // Analytics / Logs
        Route::get('/stores/{storeUuid}/abandoned-carts', [Modules\Shopify\Controllers\ShopifyController::class, 'abandonedCarts']);
        Route::get('/stores/{storeUuid}/logs', [Modules\Shopify\Controllers\ShopifyController::class, 'notificationLogs']);

        // Prebuilt templates
        Route::get('/prebuilt-template/{templateKey}', [Modules\Shopify\Controllers\ShopifyController::class, 'getPrebuiltTemplate']);
        Route::post('/submit-template', [Modules\Shopify\Controllers\ShopifyController::class, 'submitTemplate']);
    });
});
