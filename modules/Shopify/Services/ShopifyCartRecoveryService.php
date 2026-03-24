<?php

namespace Modules\Shopify\Services;

use App\Models\Contact;
use Illuminate\Support\Facades\Log;
use Modules\Shopify\Models\ShopifyAbandonedCart;
use Modules\Shopify\Models\ShopifyCartRecoverySetting;
use Modules\Shopify\Models\ShopifyStore;
use Modules\Shopify\Jobs\SendCartReminderJob;

class ShopifyCartRecoveryService
{
    protected ShopifyNotificationService $notificationService;

    public function __construct(ShopifyNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle a checkout creation/update event (potential abandoned cart).
     */
    public function handleCheckoutEvent(ShopifyStore $store, array $payload): void
    {
        $settings = ShopifyCartRecoverySetting::where('shopify_store_id', $store->id)
            ->where('is_active', true)
            ->first();

        if (!$settings) {
            return;
        }

        $checkoutId = (string) ($payload['id'] ?? $payload['token'] ?? '');
        if (!$checkoutId) {
            return;
        }

        $phone = $this->notificationService->extractPhoneFromPayload($payload);
        $contactId = null;

        if ($phone) {
            $contact = Contact::where('organization_id', $store->organization_id)
                ->where('phone', $phone)
                ->first();
            $contactId = $contact?->id;
        }

        // Upsert: if cart already exists, update it
        $cart = ShopifyAbandonedCart::updateOrCreate(
            [
                'shopify_store_id' => $store->id,
                'shopify_checkout_id' => $checkoutId,
            ],
            [
                'organization_id' => $store->organization_id,
                'shopify_cart_token' => $payload['cart_token'] ?? null,
                'customer_phone' => $phone,
                'customer_email' => $payload['email'] ?? $payload['customer']['email'] ?? null,
                'customer_name' => trim(($payload['customer']['first_name'] ?? '') . ' ' . ($payload['customer']['last_name'] ?? '')),
                'recovery_url' => $payload['abandoned_checkout_url'] ?? null,
                'total_price' => $payload['total_price'] ?? 0,
                'currency' => $payload['currency'] ?? 'USD',
                'line_items' => $payload['line_items'] ?? [],
                'contact_id' => $contactId,
                'abandoned_at' => now(),
            ]
        );

        // Only schedule reminders for new/re-abandoned carts
        if (in_array($cart->status, [ShopifyAbandonedCart::STATUS_ABANDONED])) {
            $this->scheduleReminders($cart, $settings);
        }
    }

    /**
     * Mark a cart as recovered when order is completed.
     */
    public function markCartRecovered(ShopifyStore $store, array $payload): void
    {
        $email = $payload['customer']['email'] ?? null;
        $phone = $this->notificationService->extractPhoneFromPayload($payload);

        $query = ShopifyAbandonedCart::where('shopify_store_id', $store->id)
            ->whereNotIn('status', [ShopifyAbandonedCart::STATUS_RECOVERED, ShopifyAbandonedCart::STATUS_EXPIRED]);

        if ($email) {
            $query->where(function ($q) use ($email, $phone) {
                $q->where('customer_email', $email);
                if ($phone) {
                    $q->orWhere('customer_phone', $phone);
                }
            });
        } elseif ($phone) {
            $query->where('customer_phone', $phone);
        } else {
            return;
        }

        $query->update([
            'status' => ShopifyAbandonedCart::STATUS_RECOVERED,
            'recovered_at' => now(),
        ]);
    }

    /**
     * Schedule the staged reminder jobs.
     */
    protected function scheduleReminders(ShopifyAbandonedCart $cart, ShopifyCartRecoverySetting $settings): void
    {
        // Reminder 1 - default 30 minutes
        if ($settings->reminder_1_enabled && $settings->reminder_1_template_id) {
            SendCartReminderJob::dispatch($cart->id, 1)
                ->delay(now()->addMinutes($settings->reminder_1_delay_minutes));
        }

        // Reminder 2 - default 6 hours
        if ($settings->reminder_2_enabled && $settings->reminder_2_template_id) {
            SendCartReminderJob::dispatch($cart->id, 2)
                ->delay(now()->addMinutes($settings->reminder_2_delay_minutes));
        }

        // Reminder 3 - default 24 hours (with discount)
        if ($settings->reminder_3_enabled && $settings->reminder_3_template_id) {
            SendCartReminderJob::dispatch($cart->id, 3)
                ->delay(now()->addMinutes($settings->reminder_3_delay_minutes));
        }
    }

    /**
     * Process a single cart reminder (called from job).
     */
    public function processReminder(int $cartId, int $reminderNumber): void
    {
        $cart = ShopifyAbandonedCart::find($cartId);
        if (!$cart) {
            return;
        }

        // If cart is already recovered or expired, skip
        if (in_array($cart->status, [ShopifyAbandonedCart::STATUS_RECOVERED, ShopifyAbandonedCart::STATUS_EXPIRED])) {
            return;
        }

        // Validate the reminder is in correct sequence
        $expectedStatus = match ($reminderNumber) {
            1 => ShopifyAbandonedCart::STATUS_ABANDONED,
            2 => ShopifyAbandonedCart::STATUS_REMINDER_1,
            3 => ShopifyAbandonedCart::STATUS_REMINDER_2,
            default => null,
        };

        if ($expectedStatus && $cart->status !== $expectedStatus) {
            return;
        }

        $store = $cart->shopifyStore;
        $settings = ShopifyCartRecoverySetting::where('shopify_store_id', $store->id)
            ->where('is_active', true)
            ->first();

        if (!$settings) {
            return;
        }

        $templateField = "reminder_{$reminderNumber}_template_id";
        $paramsField = "reminder_{$reminderNumber}_params";
        $templateId = $settings->$templateField;
        $params = $settings->$paramsField;

        if (!$templateId) {
            return;
        }

        // Ensure contact exists
        $contact = $this->ensureContact($cart);
        if (!$contact) {
            Log::warning("Shopify: Cannot send reminder {$reminderNumber} - no contact for cart {$cart->id}");
            return;
        }

        $whatsappService = $this->notificationService->getWhatsappService($store->organization_id);
        if (!$whatsappService) {
            return;
        }

        // Build the payload for variable resolution
        $cartPayload = $this->buildCartPayload($cart, $settings, $reminderNumber);

        // Build template content using the notification service
        $template = \App\Models\Template::find($templateId);
        if (!$template) {
            return;
        }

        $notificationTemplate = new \Modules\Shopify\Models\ShopifyNotificationTemplate([
            'template_id' => $templateId,
            'template_params' => $params,
        ]);
        $notificationTemplate->setRelation('template', $template);

        $templateContent = $this->notificationService->buildTemplateContent($notificationTemplate, $cartPayload);

        // Deduct message charge before sending
        $chargeResult = $this->notificationService->deductMessageCharge($template, "cart_reminder_{$reminderNumber}");
        if ($chargeResult === false) {
            Log::warning("Shopify: Insufficient balance for cart reminder {$reminderNumber}, cart {$cart->id}");
            $this->notificationService->logCartNotification($store, "cart_reminder_{$reminderNumber}", $cartPayload, $contact->id, 'skipped', 'Insufficient balance');
            return;
        }

        try {
            $response = $whatsappService->sendTemplateMessage($contact->uuid, $templateContent);

            $chatId = ($response->success ?? false) && isset($response->data->chat) ? $response->data->chat->id : null;
            $status = ($response->success ?? false) ? 'sent' : 'failed';
            $error = ($response->success ?? false) ? null : ($response->message ?? 'Unknown error');

            if ($response->success ?? false) {
                $newStatus = match ($reminderNumber) {
                    1 => ShopifyAbandonedCart::STATUS_REMINDER_1,
                    2 => ShopifyAbandonedCart::STATUS_REMINDER_2,
                    3 => ShopifyAbandonedCart::STATUS_REMINDER_3,
                    default => $cart->status,
                };
                $cart->update(['status' => $newStatus]);
            }

            // Log the notification
            $this->notificationService->logCartNotification($store, "cart_reminder_{$reminderNumber}", $cartPayload, $contact->id, $status, $error, $chatId);

            Log::info("Shopify: Cart reminder {$reminderNumber} for cart {$cart->id}: {$status}");
        } catch (\Exception $e) {
            Log::error("Shopify: Failed to send cart reminder {$reminderNumber}: " . $e->getMessage());
            $this->notificationService->logCartNotification($store, "cart_reminder_{$reminderNumber}", $cartPayload, $contact->id, 'failed', $e->getMessage());
        }
    }

    /**
     * Build a mock payload from cart data for variable resolution.
     */
    protected function buildCartPayload(ShopifyAbandonedCart $cart, ShopifyCartRecoverySetting $settings, int $reminderNumber): array
    {
        $payload = [
            'customer' => [
                'first_name' => explode(' ', $cart->customer_name ?? '')[0] ?? '',
                'last_name' => explode(' ', $cart->customer_name ?? '')[1] ?? '',
                'email' => $cart->customer_email,
                'phone' => $cart->customer_phone,
            ],
            'total_price' => $cart->total_price,
            'currency' => $cart->currency,
            'line_items' => $cart->line_items ?? [],
            'abandoned_checkout_url' => $cart->recovery_url,
            'recovery_url' => $cart->recovery_url,
        ];

        // Add discount info for reminder 3
        if ($reminderNumber === 3 && $settings->discount_code) {
            $payload['discount_code'] = $settings->discount_code;
            $payload['discount_percentage'] = $settings->discount_percentage;
        }

        return $payload;
    }

    /**
     * Ensure a contact exists for the abandoned cart.
     */
    protected function ensureContact(ShopifyAbandonedCart $cart): ?Contact
    {
        if ($cart->contact_id) {
            return Contact::find($cart->contact_id);
        }

        if (!$cart->customer_phone) {
            return null;
        }

        $contact = Contact::where('organization_id', $cart->organization_id)
            ->where('phone', $cart->customer_phone)
            ->first();

        if (!$contact) {
            $nameParts = explode(' ', $cart->customer_name ?? '', 2);
            $contact = Contact::create([
                'organization_id' => $cart->organization_id,
                'phone' => $cart->customer_phone,
                'first_name' => $nameParts[0] ?? '',
                'last_name' => $nameParts[1] ?? '',
                'email' => $cart->customer_email,
                'created_by' => 0,
            ]);
        }

        // Link contact to cart
        $cart->update(['contact_id' => $contact->id]);

        return $contact;
    }
}
