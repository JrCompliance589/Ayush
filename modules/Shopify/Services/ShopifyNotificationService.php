<?php

namespace Modules\Shopify\Services;

use App\Models\BalanceHistory;
use App\Models\Contact;
use App\Models\Organization;
use App\Models\Template;
use App\Models\User;
use App\Services\WhatsappService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\Shopify\Models\ShopifyNotificationLog;
use Modules\Shopify\Models\ShopifyNotificationTemplate;
use Modules\Shopify\Models\ShopifyStore;

/**
 * Log to the dedicated shopify channel.
 */
function shopify_log(string $level, string $message, array $context = []): void
{
    Log::channel('shopify')->$level("[Shopify] {$message}", $context);
}

class ShopifyNotificationService
{
    /**
     * Initialize WhatsappService for a given organization.
     */
    public function getWhatsappService(int $organizationId): ?WhatsappService
    {
        $organization = Organization::find($organizationId);
        if (!$organization) {
            shopify_log('error', 'Organization not found', ['organization_id' => $organizationId]);
            return null;
        }

        $config = $organization->metadata ? json_decode($organization->metadata, true) : [];

        $accessToken = $config['whatsapp']['access_token'] ?? null;
        $apiVersion = config('graph.api_version');
        $appId = $config['whatsapp']['app_id'] ?? null;
        $phoneNumberId = $config['whatsapp']['phone_number_id'] ?? null;
        $wabaId = $config['whatsapp']['waba_id'] ?? null;

        if (!$accessToken || !$phoneNumberId) {
            shopify_log('error', 'WhatsApp not configured', [
                'organization_id' => $organizationId,
                'has_access_token' => !empty($accessToken),
                'has_phone_number_id' => !empty($phoneNumberId),
            ]);
            return null;
        }

        shopify_log('debug', 'WhatsApp service initialized', ['organization_id' => $organizationId]);
        return new WhatsappService($accessToken, $apiVersion, $appId, $phoneNumberId, $wabaId, $organizationId);
    }

    /**
     * Process an order confirmation event from Shopify.
     */
    public function handleOrderConfirmation(ShopifyStore $store, array $payload): void
    {
        $eventType = ShopifyNotificationTemplate::EVENT_ORDER_CONFIRMATION;
        shopify_log('info', '=== ORDER CONFIRMATION EVENT ===', [
            'store' => $store->shop_domain,
            'order_id' => $payload['id'] ?? null,
            'order_number' => $payload['order_number'] ?? $payload['name'] ?? null,
            'customer' => ($payload['customer']['first_name'] ?? '') . ' ' . ($payload['customer']['last_name'] ?? ''),
            'phone' => $payload['customer']['phone'] ?? $payload['shipping_address']['phone'] ?? 'N/A',
            'total' => ($payload['total_price'] ?? '0') . ' ' . ($payload['currency'] ?? ''),
            'items_count' => count($payload['line_items'] ?? []),
        ]);

        // Check for COD orders
        if ($this->isCodOrder($payload)) {
            shopify_log('info', 'COD order detected', ['payment_gateways' => $payload['payment_gateway_names'] ?? []]);
            $this->handleCodVerification($store, $payload);
        }

        $this->sendNotification($store, $eventType, $payload);
    }

    /**
     * Process a shipping/fulfillment update from Shopify.
     */
    public function handleShippingUpdate(ShopifyStore $store, array $payload): void
    {
        shopify_log('info', '=== SHIPPING UPDATE EVENT ===', [
            'store' => $store->shop_domain,
            'order_id' => $payload['order_id'] ?? $payload['id'] ?? null,
            'tracking' => $payload['tracking_numbers'] ?? [],
        ]);
        $this->sendNotification($store, ShopifyNotificationTemplate::EVENT_SHIPPING_UPDATE, $payload);
    }

    /**
     * Process delivery status update from Shopify.
     */
    public function handleDeliveryStatus(ShopifyStore $store, array $payload): void
    {
        shopify_log('info', '=== DELIVERY STATUS EVENT ===', [
            'store' => $store->shop_domain,
            'order_id' => $payload['order_id'] ?? $payload['id'] ?? null,
            'status' => $payload['fulfillment_status'] ?? $payload['shipment_status'] ?? 'unknown',
        ]);
        $this->sendNotification($store, ShopifyNotificationTemplate::EVENT_DELIVERY_STATUS, $payload);
    }

    /**
     * Process COD verification for cash-on-delivery orders.
     */
    public function handleCodVerification(ShopifyStore $store, array $payload): void
    {
        shopify_log('info', '=== COD VERIFICATION EVENT ===', [
            'store' => $store->shop_domain,
            'order_id' => $payload['id'] ?? null,
            'payment_gateways' => $payload['payment_gateway_names'] ?? [],
        ]);
        $this->sendNotification($store, ShopifyNotificationTemplate::EVENT_COD_VERIFICATION, $payload);
    }

    /**
     * Fetch full order details from Shopify API.
     */
    protected function fetchOrderFromShopify(ShopifyStore $store, int $orderId): ?array
    {
        if (empty($store->access_token) || empty($store->shop_domain)) {
            shopify_log('warning', 'Cannot fetch order: missing store credentials', ['store_id' => $store->id]);
            return null;
        }

        $apiVersion = config('shopify.api_version', '2024-01');
        $url = "https://{$store->shop_domain}/admin/api/{$apiVersion}/orders/{$orderId}.json";

        try {
            $response = Http::withHeaders([
                'X-Shopify-Access-Token' => $store->access_token,
                'Content-Type' => 'application/json',
            ])->timeout(10)->get($url);

            if ($response->successful()) {
                $order = $response->json('order');
                shopify_log('info', 'Fetched order from Shopify API', [
                    'order_id' => $orderId,
                    'order_number' => $order['order_number'] ?? null,
                    'customer' => ($order['customer']['first_name'] ?? '') . ' ' . ($order['customer']['last_name'] ?? ''),
                ]);
                return $order;
            }

            shopify_log('error', 'Failed to fetch order from Shopify API', [
                'order_id' => $orderId,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        } catch (\Exception $e) {
            shopify_log('error', 'Exception fetching order from Shopify', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }

    /**
     * Enrich a webhook payload with full order details from Shopify API.
     * Fulfillment payloads lack customer, pricing, and address data.
     */
    protected function enrichPayloadWithOrderData(ShopifyStore $store, array $payload): array
    {
        $orderId = $payload['order_id'] ?? $payload['id'] ?? null;
        if (!$orderId) {
            return $payload;
        }

        // If the payload already has customer data, no need to fetch
        if (!empty($payload['customer']['phone']) || !empty($payload['shipping_address']['phone'])) {
            return $payload;
        }

        shopify_log('info', 'Payload missing customer data, fetching order from Shopify API', ['order_id' => $orderId]);

        $order = $this->fetchOrderFromShopify($store, $orderId);
        if (!$order) {
            return $payload;
        }

        // Merge order data into payload, keeping original fulfillment-specific fields
        $enriched = array_merge($order, $payload);

        // Ensure critical fields come from the order
        $enriched['customer'] = $order['customer'] ?? $payload['customer'] ?? null;
        $enriched['shipping_address'] = $order['shipping_address'] ?? $payload['shipping_address'] ?? null;
        $enriched['billing_address'] = $order['billing_address'] ?? $payload['billing_address'] ?? null;
        $enriched['order_number'] = $order['order_number'] ?? $payload['order_number'] ?? null;
        $enriched['order_status_url'] = $order['order_status_url'] ?? $payload['order_status_url'] ?? null;
        $enriched['total_price'] = $order['total_price'] ?? $payload['total_price'] ?? null;
        $enriched['currency'] = $order['currency'] ?? $payload['currency'] ?? null;
        $enriched['payment_gateway_names'] = $order['payment_gateway_names'] ?? $payload['payment_gateway_names'] ?? [];

        // Keep fulfillment-specific tracking data from original payload
        $enriched['tracking_numbers'] = $payload['tracking_numbers'] ?? $order['tracking_numbers'] ?? [];
        $enriched['tracking_urls'] = $payload['tracking_urls'] ?? $order['tracking_urls'] ?? [];
        $enriched['tracking_company'] = $payload['tracking_company'] ?? $order['tracking_company'] ?? null;
        $enriched['shipment_status'] = $payload['shipment_status'] ?? null;

        shopify_log('info', 'Payload enriched with order data', [
            'order_id' => $orderId,
            'has_customer' => !empty($enriched['customer']),
            'has_shipping_address' => !empty($enriched['shipping_address']),
            'has_phone' => !empty($enriched['customer']['phone'] ?? $enriched['shipping_address']['phone'] ?? null),
        ]);

        return $enriched;
    }

    /**
     * Core method: send a notification for a given event type.
     */
    public function sendNotification(ShopifyStore $store, string $eventType, array $payload): void
    {
        shopify_log('info', "--- sendNotification START [{$eventType}] ---", ['store' => $store->shop_domain]);

        $notificationTemplate = ShopifyNotificationTemplate::where('shopify_store_id', $store->id)
            ->where('event_type', $eventType)
            ->where('is_active', true)
            ->first();

        if (!$notificationTemplate || !$notificationTemplate->template_id) {
            shopify_log('warning', "No active template for event [{$eventType}]", [
                'store_id' => $store->id,
                'store' => $store->shop_domain,
            ]);
            return;
        }

        shopify_log('info', 'Found notification template', [
            'notification_template_id' => $notificationTemplate->id,
            'whatsapp_template_id' => $notificationTemplate->template_id,
            'template_params' => $notificationTemplate->template_params,
        ]);

        // Enrich payload with full order data from Shopify API if needed
        $payload = $this->enrichPayloadWithOrderData($store, $payload);

        $phone = $this->extractPhoneFromPayload($payload);
        if (!$phone) {
            shopify_log('warning', 'No phone number in payload', [
                'event' => $eventType,
                'customer_phone' => $payload['customer']['phone'] ?? null,
                'shipping_phone' => $payload['shipping_address']['phone'] ?? null,
                'billing_phone' => $payload['billing_address']['phone'] ?? null,
            ]);
            $this->logNotification($store, $eventType, $payload, null, 'skipped', 'No phone number found');
            return;
        }

        shopify_log('info', 'Phone extracted', ['phone' => $phone]);

        $contact = Contact::where('organization_id', $store->organization_id)
            ->where('phone', $phone)
            ->first();

        if (!$contact) {
            shopify_log('info', 'Contact not found, auto-creating', ['phone' => $phone]);
            $contact = $this->createContactFromPayload($store->organization_id, $payload);
        }

        if (!$contact) {
            shopify_log('error', 'Failed to find or create contact', ['phone' => $phone]);
            $this->logNotification($store, $eventType, $payload, null, 'skipped', 'Contact not found');
            return;
        }

        shopify_log('info', 'Contact resolved', ['contact_id' => $contact->id, 'uuid' => $contact->uuid]);

        $whatsappService = $this->getWhatsappService($store->organization_id);
        if (!$whatsappService) {
            $this->logNotification($store, $eventType, $payload, $contact->id, 'failed', 'WhatsApp not configured');
            return;
        }

        $templateContent = $this->buildTemplateContent($notificationTemplate, $payload);

        // Deduct message charge before sending
        $template = $notificationTemplate->template;
        $chargeResult = $this->deductMessageCharge($template, $eventType);
        if ($chargeResult === false) {
            shopify_log('warning', 'Insufficient balance, skipping notification', ['event' => $eventType]);
            $this->logNotification($store, $eventType, $payload, $contact->id, 'skipped', 'Insufficient balance');
            return;
        }

        shopify_log('info', 'Sending WhatsApp template message', [
            'contact_uuid' => $contact->uuid,
            'template_name' => $templateContent['name'] ?? 'unknown',
            'components' => json_encode($templateContent['components'] ?? []),
        ]);

        try {
            $response = $whatsappService->sendTemplateMessage($contact->uuid, $templateContent);

            $chatId = $response->success && isset($response->data->chat) ? $response->data->chat->id : null;
            $status = $response->success ? 'sent' : 'failed';
            $error = $response->success ? null : ($response->message ?? 'Unknown error');

            shopify_log($response->success ? 'info' : 'error', "Message result: {$status}", [
                'chat_id' => $chatId,
                'error' => $error,
            ]);

            $this->logNotification($store, $eventType, $payload, $contact->id, $status, $error, $chatId);
        } catch (\Exception $e) {
            shopify_log('error', 'Exception sending notification', [
                'event' => $eventType,
                'error' => $e->getMessage(),
            ]);
            $this->logNotification($store, $eventType, $payload, $contact->id, 'failed', $e->getMessage());
        }

        shopify_log('info', "--- sendNotification END [{$eventType}] ---");
    }

    /**
     * Build WhatsApp template content with parameters from Shopify payload.
     */
    public function buildTemplateContent(ShopifyNotificationTemplate $notificationTemplate, array $payload): array
    {
        $template = $notificationTemplate->template;
        if (!$template) {
            shopify_log('error', 'WhatsApp template record not found', ['template_id' => $notificationTemplate->template_id]);
            return [];
        }

        $templateMeta = json_decode($template->metadata, true);
        $paramMapping = $notificationTemplate->template_params ?? [];

        shopify_log('debug', 'Building template content', [
            'template_name' => $template->name,
            'template_language' => $template->language,
            'param_mapping' => $paramMapping,
            'meta_components' => array_map(fn($c) => $c['type'] ?? 'unknown', $templateMeta['components'] ?? []),
        ]);

        $content = [
            'name' => $template->name,
            'language' => ['code' => $template->language],
            'components' => [],
        ];

        // Build components with parameter replacements
        if (!empty($templateMeta['components'])) {
            foreach ($templateMeta['components'] as $component) {
                if ($component['type'] === 'BODY' && !empty($paramMapping['body'])) {
                    $parameters = [];
                    foreach ($paramMapping['body'] as $index => $mapping) {
                        $value = $this->resolveVariable($mapping, $payload);
                        // Meta rejects empty text parameters
                        if ($value === '') $value = '-';
                        shopify_log('debug', "Body var {{" . ($index + 1) . "}}: '{$mapping}' => '{$value}'");
                        $parameters[] = ['type' => 'text', 'text' => $value];
                    }
                    if (!empty($parameters)) {
                        $content['components'][] = [
                            'type' => 'body',
                            'parameters' => $parameters,
                        ];
                    }
                }

                if ($component['type'] === 'HEADER' && ($component['format'] ?? '') === 'TEXT' && !empty($paramMapping['header'])) {
                    $parameters = [];
                    foreach ($paramMapping['header'] as $index => $mapping) {
                        $value = $this->resolveVariable($mapping, $payload);
                        shopify_log('debug', "Header var {{" . ($index + 1) . "}}: '{$mapping}' => '{$value}'");
                        $parameters[] = ['type' => 'text', 'text' => $value];
                    }
                    if (!empty($parameters)) {
                        $content['components'][] = [
                            'type' => 'header',
                            'parameters' => $parameters,
                        ];
                    }
                }

                if ($component['type'] === 'BUTTONS' && !empty($paramMapping['buttons'])) {
                    foreach ($paramMapping['buttons'] as $index => $buttonMapping) {
                        $value = $this->resolveVariable($buttonMapping, $payload);
                        // For URL buttons, strip the domain and only pass the path+query
                        $value = $this->extractUrlPath($value);
                        // Meta rejects empty text parameters
                        if ($value === '') $value = '/';
                        shopify_log('debug', "Button var [{$index}]: '{$buttonMapping}' => '{$value}'");
                        $content['components'][] = [
                            'type' => 'button',
                            'sub_type' => 'url',
                            'index' => $index,
                            'parameters' => [
                                ['type' => 'text', 'text' => $value],
                            ],
                        ];
                    }
                }
            }
        }

        shopify_log('info', 'Template content built', [
            'name' => $content['name'],
            'components_count' => count($content['components']),
        ]);

        return $content;
    }

    /**
     * Resolve a template variable to its value from the Shopify payload.
     */
    public function resolveVariable(string $variable, array $payload): string
    {
        // Normalize: ensure variable has {{ }} wrapper for lookup
        // template_params may store 'customer_first_name' or '{{customer_first_name}}'
        $normalized = $variable;
        if (!str_starts_with($variable, '{{')) {
            $normalized = '{{' . $variable . '}}';
        }

        $map = [
            '{{order_number}}' => $payload['order_number'] ?? $payload['name'] ?? '',
            '{{customer_name}}' => trim(($payload['customer']['first_name'] ?? '') . ' ' . ($payload['customer']['last_name'] ?? '')) ?: 'Customer',
            '{{customer_first_name}}' => $payload['customer']['first_name'] ?? '' ?: 'there',
            '{{total_price}}' => $payload['total_price'] ?? $payload['current_total_price'] ?? '',
            '{{currency}}' => $payload['currency'] ?? '',
            '{{item_count}}' => isset($payload['line_items']) ? (string) count($payload['line_items']) : '',
            '{{items_summary}}' => $this->buildItemsSummary($payload['line_items'] ?? []),
            '{{order_status_url}}' => $payload['order_status_url'] ?? '',
            '{{payment_method}}' => $payload['payment_gateway_names'][0] ?? '',
            '{{tracking_number}}' => $this->extractTracking($payload, 'number'),
            '{{tracking_url}}' => $this->extractTracking($payload, 'url'),
            '{{carrier}}' => $this->extractTracking($payload, 'company'),
            '{{estimated_delivery}}' => $payload['estimated_delivery_at'] ?? '',
            '{{delivery_status}}' => $payload['fulfillment_status'] ?? $payload['shipment_status'] ?? '',
            '{{delivery_address}}' => $this->formatAddress($payload['shipping_address'] ?? []),
            '{{recovery_url}}' => $payload['abandoned_checkout_url'] ?? $payload['recovery_url'] ?? '',
            '{{cart_total}}' => $payload['total_price'] ?? '',
            '{{discount_code}}' => $payload['discount_code'] ?? '',
            '{{discount_percentage}}' => $payload['discount_percentage'] ?? '',
        ];

        if (!isset($map[$normalized])) {
            shopify_log('warning', "Variable not resolved, returning empty", ['input' => $variable, 'normalized' => $normalized]);
            return '';
        }

        return (string) $map[$normalized];
    }

    /**
     * Extract phone number from various Shopify webhook payload structures.
     */
    public function extractPhoneFromPayload(array $payload): ?string
    {
        // Try customer phone
        if (!empty($payload['customer']['phone'])) {
            return $this->normalizePhone($payload['customer']['phone']);
        }

        // Try shipping address phone
        if (!empty($payload['shipping_address']['phone'])) {
            return $this->normalizePhone($payload['shipping_address']['phone']);
        }

        // Try billing address phone
        if (!empty($payload['billing_address']['phone'])) {
            return $this->normalizePhone($payload['billing_address']['phone']);
        }

        // For checkout events
        if (!empty($payload['phone'])) {
            return $this->normalizePhone($payload['phone']);
        }

        return null;
    }

    /**
     * Create a contact in the system from Shopify customer data.
     */
    protected function createContactFromPayload(int $organizationId, array $payload): ?Contact
    {
        $phone = $this->extractPhoneFromPayload($payload);
        if (!$phone) {
            return null;
        }

        $customer = $payload['customer'] ?? [];
        $firstName = $customer['first_name'] ?? '';
        $lastName = $customer['last_name'] ?? '';

        return Contact::create([
            'organization_id' => $organizationId,
            'phone' => $phone,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $customer['email'] ?? null,
            'created_by' => 0,
        ]);
    }

    protected function isCodOrder(array $payload): bool
    {
        $gateway = strtolower($payload['payment_gateway_names'][0] ?? '');
        return str_contains($gateway, 'cod') || str_contains($gateway, 'cash on delivery');
    }

    protected function normalizePhone(string $phone): string
    {
        // Remove spaces, dashes, and ensure it starts with country code
        return preg_replace('/[^0-9+]/', '', $phone);
    }

    protected function buildItemsSummary(array $lineItems): string
    {
        $items = [];
        foreach (array_slice($lineItems, 0, 5) as $item) {
            $qty = $item['quantity'] ?? 1;
            $name = $item['title'] ?? $item['name'] ?? 'Item';
            $items[] = "{$qty}x {$name}";
        }
        $summary = implode(', ', $items);
        if (count($lineItems) > 5) {
            $remaining = count($lineItems) - 5;
            $summary .= " +{$remaining} more";
        }
        return $summary;
    }

    /**
     * Extract path+query from a full URL for use as a button URL parameter.
     * WhatsApp URL buttons have a static domain prefix; only the path is dynamic.
     * e.g. "https://store.com/orders/123?key=abc" → "orders/123?key=abc"
     * Returns original value if it's not a valid URL.
     */
    protected function extractUrlPath(string $value): string
    {
        if (empty($value) || !preg_match('#^https?://#i', $value)) {
            return $value;
        }

        $parsed = parse_url($value);
        if ($parsed === false || empty($parsed['path'])) {
            return $value;
        }

        $path = ltrim($parsed['path'], '/');
        if (!empty($parsed['query'])) {
            $path .= '?' . $parsed['query'];
        }
        if (!empty($parsed['fragment'])) {
            $path .= '#' . $parsed['fragment'];
        }

        return $path;
    }

    protected function extractTracking(array $payload, string $field): string
    {
        // Fulfillment events
        if (!empty($payload['tracking_numbers']) && $field === 'number') {
            return $payload['tracking_numbers'][0] ?? '';
        }
        if (!empty($payload['tracking_urls']) && $field === 'url') {
            return $payload['tracking_urls'][0] ?? '';
        }
        if (!empty($payload['tracking_company']) && $field === 'company') {
            return $payload['tracking_company'] ?? '';
        }

        // Nested fulfillments in order payload
        $fulfillments = $payload['fulfillments'] ?? [];
        if (!empty($fulfillments)) {
            $latest = end($fulfillments);
            if ($field === 'number') return $latest['tracking_number'] ?? '';
            if ($field === 'url') return $latest['tracking_url'] ?? '';
            if ($field === 'company') return $latest['tracking_company'] ?? '';
        }

        return '';
    }

    protected function formatAddress(array $address): string
    {
        $parts = array_filter([
            $address['address1'] ?? '',
            $address['address2'] ?? '',
            $address['city'] ?? '',
            $address['province'] ?? '',
            $address['zip'] ?? '',
            $address['country'] ?? '',
        ]);
        return implode(', ', $parts);
    }

    /**
     * Public wrapper to log a cart reminder notification.
     */
    public function logCartNotification(
        ShopifyStore $store,
        string $eventType,
        array $payload,
        ?int $contactId,
        string $status,
        ?string $errorMessage = null,
        ?int $chatId = null
    ): void {
        $this->logNotification($store, $eventType, $payload, $contactId, $status, $errorMessage, $chatId);
    }

    /**
     * Deduct message charge based on template category.
     * Returns false if insufficient balance, true otherwise.
     */
    public function deductMessageCharge(Template $template, string $eventType): bool
    {
        $userId = $template->created_by;
        $user = $userId ? User::find($userId) : null;

        if (!$user) {
            shopify_log('warning', 'No user found for charge deduction', ['template_id' => $template->id, 'created_by' => $userId]);
            return true; // Don't block if user not found
        }

        $category = strtolower((string) ($template->category ?? 'marketing'));

        $price = match ($category) {
            'marketing' => (float) ($user->marketing_price ?? 0),
            'utility' => (float) ($user->utility_price ?? 0),
            'authentication', 'auth' => (float) ($user->auth_price ?? 0),
            default => (float) ($user->marketing_price ?? 0),
        };

        if ($price <= 0) {
            shopify_log('debug', 'Price is zero, no charge', ['category' => $category]);
            return true;
        }

        $charge = round($price, 2);
        $oldBalance = (float) $user->balance;

        if ($oldBalance < $charge) {
            shopify_log('warning', 'Insufficient balance', [
                'user_id' => $userId,
                'balance' => $oldBalance,
                'charge' => $charge,
                'category' => $category,
            ]);
            return false;
        }

        $newBalance = round($oldBalance - $charge, 2);
        $user->balance = $newBalance;
        $user->save();

        BalanceHistory::create([
            'user_id' => $userId,
            'amount' => -$charge,
            'balance_after' => $newBalance,
            'type' => 'debit',
            'note' => "Shopify {$eventType} - {$template->name} ({$category})",
        ]);

        shopify_log('info', 'Charge deducted', [
            'user_id' => $userId,
            'charge' => $charge,
            'category' => $category,
            'old_balance' => $oldBalance,
            'new_balance' => $newBalance,
        ]);

        return true;
    }

    protected function logNotification(
        ShopifyStore $store,
        string $eventType,
        array $payload,
        ?int $contactId,
        string $status,
        ?string $errorMessage = null,
        ?int $chatId = null
    ): void {
        $resourceId = $payload['id'] ?? $payload['checkout_token'] ?? null;

        ShopifyNotificationLog::create([
            'organization_id' => $store->organization_id,
            'shopify_store_id' => $store->id,
            'event_type' => $eventType,
            'shopify_resource_id' => $resourceId,
            'contact_id' => $contactId,
            'chat_id' => $chatId,
            'status' => $status,
            'error_message' => $errorMessage,
            'payload' => $payload,
        ]);
    }
}
