<?php

namespace Modules\Shopify\Controllers;

use App\Http\Controllers\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Shopify\Jobs\ProcessShopifyWebhookJob;
use Modules\Shopify\Models\ShopifyStore;

class WebhookController extends BaseController
{
    /**
     * Receive and validate Shopify webhooks.
     * This endpoint is open (no auth middleware) — secured by HMAC verification.
     */
    public function handle(Request $request, string $storeUuid)
    {
        Log::channel('shopify')->info('====== WEBHOOK RECEIVED ======', [
            'store_uuid' => $storeUuid,
            'topic' => $request->header('X-Shopify-Topic', 'none'),
            'ip' => $request->ip(),
        ]);

        $store = ShopifyStore::where('uuid', $storeUuid)
            ->where('is_active', true)
            ->first();

        if (!$store) {
            Log::channel('shopify')->warning('Store not found or inactive', ['uuid' => $storeUuid]);
            return response()->json(['error' => 'Store not found'], 404);
        }

        // Verify HMAC signature
        $hmacHeader = $request->header('X-Shopify-Hmac-Sha256', '');
        $rawBody = $request->getContent();

        if ($store->webhook_secret) {
            $calculatedHmac = base64_encode(hash_hmac('sha256', $rawBody, $store->webhook_secret, true));
            if (!hash_equals($calculatedHmac, $hmacHeader)) {
                Log::channel('shopify')->warning('HMAC verification failed', ['store' => $store->shop_domain]);
                return response()->json(['error' => 'Invalid signature'], 401);
            }
            Log::channel('shopify')->debug('HMAC verified OK', ['store' => $store->shop_domain]);
        }

        $topic = $request->header('X-Shopify-Topic', '');
        $payload = $request->all();

        if (!$topic) {
            Log::channel('shopify')->warning('Missing topic header');
            return response()->json(['error' => 'Missing topic'], 400);
        }

        Log::channel('shopify')->info('Dispatching webhook job', [
            'store' => $store->shop_domain,
            'topic' => $topic,
            'payload_keys' => array_keys($payload),
            'order_id' => $payload['id'] ?? null,
            'order_number' => $payload['order_number'] ?? $payload['name'] ?? null,
        ]);

        // Dispatch to queue for async processing
        ProcessShopifyWebhookJob::dispatch($store->id, $topic, $payload);

        return response()->json(['status' => 'received'], 200);
    }
}
