<?php

namespace Modules\Shopify\Services;

use Illuminate\Support\Facades\Log;
use Modules\Shopify\Models\ShopifyStore;

class ShopifyWebhookService
{
    protected ShopifyNotificationService $notificationService;
    protected ShopifyCartRecoveryService $cartRecoveryService;

    public function __construct(
        ShopifyNotificationService $notificationService,
        ShopifyCartRecoveryService $cartRecoveryService
    ) {
        $this->notificationService = $notificationService;
        $this->cartRecoveryService = $cartRecoveryService;
    }

    /**
     * Verify the Shopify webhook HMAC signature.
     */
    public function verifyWebhook(string $rawBody, string $hmacHeader, string $secret): bool
    {
        $calculatedHmac = base64_encode(hash_hmac('sha256', $rawBody, $secret, true));
        return hash_equals($calculatedHmac, $hmacHeader);
    }

    /**
     * Route a Shopify webhook to the appropriate handler.
     */
    public function processWebhook(ShopifyStore $store, string $topic, array $payload): void
    {
        Log::channel('shopify')->info("Processing webhook [{$topic}] for store {$store->shop_domain}", [
            'store_id' => $store->id,
            'topic' => $topic,
            'order_id' => $payload['id'] ?? null,
            'order_number' => $payload['order_number'] ?? $payload['name'] ?? null,
        ]);

        // Check if this event type is enabled for the store
        $enabledEvents = $store->enabled_events ?? [];
        if (!empty($enabledEvents) && !in_array($topic, $enabledEvents)) {
            Log::channel('shopify')->info("Event [{$topic}] not enabled for store", [
                'enabled_events' => $enabledEvents,
            ]);
            return;
        }

        match ($topic) {
            'orders/create', 'orders/paid' => $this->handleOrderEvent($store, $payload),
            'fulfillments/create' => $this->notificationService->handleShippingUpdate($store, $payload),
            'fulfillments/update' => $this->handleFulfillmentUpdate($store, $payload),
            'orders/fulfilled' => $this->handleOrderFulfilled($store, $payload),
            'checkouts/create', 'checkouts/update' => $this->cartRecoveryService->handleCheckoutEvent($store, $payload),
            default => Log::channel('shopify')->info("Unhandled webhook topic [{$topic}]"),
        };
    }

    /**
     * Handle order creation event.
     */
    protected function handleOrderEvent(ShopifyStore $store, array $payload): void
    {
        // Mark any related abandoned carts as recovered
        $this->cartRecoveryService->markCartRecovered($store, $payload);

        // Send order confirmation
        $this->notificationService->handleOrderConfirmation($store, $payload);
    }

    /**
     * Handle fulfillment updates - determine if it's shipping or delivery.
     */
    protected function handleFulfillmentUpdate(ShopifyStore $store, array $payload): void
    {
        $shipmentStatus = $payload['shipment_status'] ?? null;

        if ($shipmentStatus === 'delivered') {
            $this->notificationService->handleDeliveryStatus($store, $payload);
        } else {
            $this->notificationService->handleShippingUpdate($store, $payload);
        }
    }

    /**
     * Handle order fulfilled event.
     */
    protected function handleOrderFulfilled(ShopifyStore $store, array $payload): void
    {
        $this->notificationService->handleDeliveryStatus($store, $payload);
    }
}
