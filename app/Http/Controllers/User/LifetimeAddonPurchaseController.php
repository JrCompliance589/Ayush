<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller as BaseController;
use App\Models\LifetimeAddon;
use App\Models\Setting;
use App\Models\UserLifetimeAddon;
use App\Services\LifetimeSubscriptionService;
use GuzzleHttp\Client as HttpClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class LifetimeAddonPurchaseController extends BaseController
{
    protected $razorpayKeyId;
    protected $razorpayKeySecret;
    protected $currency;

    public function __construct()
    {
        $settings = Setting::whereIn('key', [
            'razorpay_key_id',
            'razorpay_secret_key',
            'currency'
        ])->pluck('value', 'key');

        $this->razorpayKeyId = $settings['razorpay_key_id'] ?? config('services.razorpay.key_id');
        $this->razorpayKeySecret = $settings['razorpay_secret_key'] ?? config('services.razorpay.key_secret');
        $this->currency = $settings['currency'] ?? 'INR';
    }

    /**
     * Display available addons for purchase.
     */
    public function index()
    {
        $organizationId = session()->get('current_organization');

        // Check if user has lifetime subscription
        $hasLifetime = LifetimeSubscriptionService::hasLifetimeSubscription($organizationId);

        if (!$hasLifetime) {
            // Show page with message for non-lifetime users
            return Inertia::render('User/LifetimeAddons/Index', [
                'title' => __('Lifetime Addons'),
                'hasLifetime' => false,
                'addons' => [],
                'purchasedAddons' => [],
                'usageSummary' => null,
                'razorpayKeyId' => $this->razorpayKeyId,
                'currency' => $this->currency,
            ]);
        }

        $addons = LifetimeAddon::active()
            ->whereNull('deleted_at')
            ->get()
            ->map(function ($addon) {
                return [
                    'id' => $addon->id,
                    'uuid' => $addon->uuid,
                    'name' => $addon->name,
                    'description' => $addon->description,
                    'type' => $addon->type,
                    'type_label' => $addon->type === 'campaign_limit' ? 'Daily Campaigns' : 'Daily Contacts',
                    'quantity' => $addon->quantity,
                    'price' => $addon->price,
                ];
            });

        $purchasedAddons = LifetimeSubscriptionService::getOrganizationAddons($organizationId);
        $usageSummary = LifetimeSubscriptionService::getTodayUsageSummary($organizationId);

        return Inertia::render('User/LifetimeAddons/Index', [
            'title' => __('Purchase Addons'),
            'hasLifetime' => true,
            'addons' => $addons,
            'purchasedAddons' => $purchasedAddons,
            'usageSummary' => $usageSummary,
            'razorpayKeyId' => $this->razorpayKeyId,
            'currency' => $this->currency,
        ]);
    }

    /**
     * Create Razorpay payment link for addon purchase.
     */
    public function createPayment(Request $request)
    {
        $request->validate([
            'addon_id' => 'required|exists:lifetime_addons,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $organizationId = session()->get('current_organization');
        $addon = LifetimeAddon::findOrFail($request->addon_id);

        $totalPrice = $addon->price * $request->quantity;
        $totalQuantity = $addon->quantity * $request->quantity;

        try {
            // Create pending purchase record
            $purchase = UserLifetimeAddon::create([
                'organization_id' => $organizationId,
                'addon_id' => $addon->id,
                'quantity' => $totalQuantity,
                'price_paid' => $totalPrice,
                'status' => 'pending',
            ]);

            // Create Razorpay payment link
            $httpClient = new HttpClient();
            $response = $httpClient->request('POST', 'https://api.razorpay.com/v1/payment_links', [
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode($this->razorpayKeyId . ':' . $this->razorpayKeySecret),
                    'Content-Type' => 'application/json',
                    'Cache-Control' => 'no-cache'
                ],
                'body' => json_encode([
                    'amount' => (int) ($totalPrice * 100), // Amount in smallest currency unit
                    'currency' => $this->currency,
                    'description' => 'Addon Purchase: ' . $addon->name . ' (x' . $request->quantity . ')',
                    'callback_url' => url('lifetime-addons/payment-callback'),
                    'callback_method' => 'get',
                    'customer' => [
                        'name' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
                        'email' => auth()->user()->email,
                    ],
                    'notes' => [
                        'organization_id' => $organizationId,
                        'user_id' => auth()->user()->id,
                        'purchase_id' => $purchase->id,
                        'addon_id' => $addon->id,
                        'type' => 'lifetime_addon',
                    ],
                    'reminder_enable' => false
                ])
            ]);

            $statusCode = $response->getStatusCode();

            if ($statusCode == 200 || $statusCode == 201) {
                $paymentLink = json_decode($response->getBody()->getContents(), true);

                return response()->json([
                    'success' => true,
                    'payment_url' => $paymentLink['short_url'],
                    'purchase_id' => $purchase->id,
                ]);
            }

            // Clean up pending purchase if payment creation fails
            $purchase->delete();

            return response()->json([
                'success' => false,
                'message' => 'Unable to create payment link',
            ], 500);
        } catch (\Exception $e) {
            Log::error('Error creating addon payment: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Payment creation failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle payment callback from Razorpay.
     */
    public function paymentCallback(Request $request)
    {
        $paymentId = $request->get('razorpay_payment_id');

        if (!$paymentId) {
            return redirect('/lifetime-addons')->with('error', __('Payment failed or cancelled.'));
        }

        try {
            // Verify payment with Razorpay
            $httpClient = new HttpClient();
            $response = $httpClient->request('GET', 'https://api.razorpay.com/v1/payments/' . $paymentId, [
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode($this->razorpayKeyId . ':' . $this->razorpayKeySecret),
                ],
            ]);

            $payment = json_decode($response->getBody()->getContents(), true);

            if ($payment['status'] === 'captured' || $payment['status'] === 'authorized') {
                $notes = $payment['notes'];
                $purchaseId = $notes['purchase_id'] ?? null;

                if ($purchaseId) {
                    LifetimeSubscriptionService::completePurchase($purchaseId, $paymentId);

                    return redirect('/lifetime-addons')->with('success', __('Addon purchased successfully!'));
                }
            }

            return redirect('/lifetime-addons')->with('error', __('Payment verification failed.'));
        } catch (\Exception $e) {
            Log::error('Error verifying addon payment: ' . $e->getMessage());
            return redirect('/lifetime-addons')->with('error', __('Payment verification failed.'));
        }
    }

    /**
     * Handle Razorpay webhook for addon purchases.
     */
    public function handleWebhook(Request $request)
    {
        $payload = json_decode($request->getContent(), true);
        $signature = $request->header('X-Razorpay-Signature');

        $webhookSecret = Setting::where('key', 'razorpay_webhook_secret')->value('value');

        if (is_null($signature)) {
            Log::error('Lifetime Addon Webhook signature is missing.');
            return response()->json(['status' => 'error', 'message' => 'Missing signature'], 400);
        }

        $computedSignature = hash_hmac('sha256', $request->getContent(), $webhookSecret);

        if (hash_equals($computedSignature, $signature)) {
            if ($payload['event'] === 'payment.authorized' || $payload['event'] === 'payment.captured') {
                $notes = $payload['payload']['payment']['entity']['notes'] ?? [];

                // Check if this is a lifetime addon purchase
                if (isset($notes['type']) && $notes['type'] === 'lifetime_addon') {
                    $purchaseId = $notes['purchase_id'] ?? null;
                    $paymentId = $payload['payload']['payment']['entity']['id'];

                    if ($purchaseId) {
                        try {
                            LifetimeSubscriptionService::completePurchase($purchaseId, $paymentId);
                            Log::info('Lifetime addon purchase completed via webhook', [
                                'purchase_id' => $purchaseId,
                                'payment_id' => $paymentId,
                            ]);
                        } catch (\Exception $e) {
                            Log::error('Error completing addon purchase via webhook: ' . $e->getMessage());
                        }
                    }
                }
            }
        } else {
            Log::error('Invalid Lifetime Addon Webhook signature.');
            return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 400);
        }

        return response()->json(['status' => 200], 200);
    }

    /**
     * Get current usage and limits for the organization.
     */
    public function getUsageSummary()
    {
        $organizationId = session()->get('current_organization');
        $summary = LifetimeSubscriptionService::getTodayUsageSummary($organizationId);

        return response()->json([
            'success' => true,
            'data' => $summary,
        ]);
    }
}
