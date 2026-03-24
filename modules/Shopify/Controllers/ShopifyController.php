<?php

namespace Modules\Shopify\Controllers;

use App\Helpers\CustomHelper;
use App\Http\Controllers\Controller as BaseController;
use App\Models\Organization;
use App\Models\Template;
use App\Services\WhatsappService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Modules\Shopify\Models\ShopifyAbandonedCart;
use Modules\Shopify\Models\ShopifyCartRecoverySetting;
use Modules\Shopify\Models\ShopifyNotificationLog;
use Modules\Shopify\Models\ShopifyNotificationTemplate;
use Modules\Shopify\Models\ShopifyStore;

class ShopifyController extends BaseController
{
    /**
     * Display the Shopify integration dashboard.
     */
    public function index(Request $request)
    {
        $organizationId = session()->get('current_organization');

        $stores = ShopifyStore::where('organization_id', $organizationId)
            ->withCount(['notificationLogs', 'abandonedCarts'])
            ->get();

        $templates = Template::where('organization_id', $organizationId)
            ->where('status', 'APPROVED')
            ->get(['id', 'name', 'language', 'category', 'status']);

        // All templates including PENDING for status display
        $allTemplates = Template::where('organization_id', $organizationId)
            ->whereIn('status', ['APPROVED', 'PENDING', 'REJECTED'])
            ->get(['id', 'name', 'language', 'category', 'status']);

        // Replace {{STORE_DOMAIN}} placeholder with actual store domain in prebuilt templates
        $prebuiltTemplates = config('shopify.prebuilt_templates');
        $activeStore = ShopifyStore::where('organization_id', $organizationId)
            ->where('is_active', true)
            ->first();
        if ($activeStore && $activeStore->shop_domain) {
            $storeDomain = rtrim($activeStore->shop_domain, '/');
            array_walk_recursive($prebuiltTemplates, function (&$value) use ($storeDomain) {
                if (is_string($value)) {
                    $value = str_replace('{{STORE_DOMAIN}}', $storeDomain, $value);
                }
            });
        }

        return Inertia::render('Shopify::User/Index', [
            'stores' => $stores,
            'templates' => $templates,
            'allTemplates' => $allTemplates,
            'eventTypes' => ShopifyNotificationTemplate::EVENT_TYPES,
            'templateVariables' => config('shopify.template_variables'),
            'webhookEvents' => config('shopify.webhook_events'),
            'prebuiltTemplates' => $prebuiltTemplates,
        ]);
    }

    /**
     * Redirect the user to Shopify's OAuth authorization page.
     */
    public function oauthRedirect(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shop_domain' => 'required|string|max:255',
            'client_id' => 'required|string|max:255',
            'client_secret' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $shopDomain = rtrim($request->shop_domain, '/');
        if (!str_contains($shopDomain, '.myshopify.com')) {
            $shopDomain = $shopDomain . '.myshopify.com';
        }

        $nonce = Str::random(40);
        $scopes = 'read_orders,write_orders,read_checkouts,write_checkouts,read_fulfillments,read_customers';
        $redirectUri = url('/integrations/shopify/oauth/callback');

        // Store OAuth state in session
        session([
            'shopify_oauth_state' => $nonce,
            'shopify_oauth_shop' => $shopDomain,
            'shopify_oauth_client_id' => $request->client_id,
            'shopify_oauth_client_secret' => $request->client_secret,
        ]);

        $authUrl = "https://{$shopDomain}/admin/oauth/authorize?" . http_build_query([
            'client_id' => $request->client_id,
            'scope' => $scopes,
            'redirect_uri' => $redirectUri,
            'state' => $nonce,
        ]);

        return Inertia::location($authUrl);
    }

    /**
     * Handle the OAuth callback from Shopify, exchange code for access token.
     */
    public function oauthCallback(Request $request)
    {
        $state = $request->query('state');
        $code = $request->query('code');
        $shopDomain = $request->query('shop');

        // Verify state to prevent CSRF
        if (!$state || $state !== session('shopify_oauth_state')) {
            return redirect('/integrations/shopify')->with('status', [
                'type' => 'error',
                'message' => __('OAuth verification failed. Please try again.'),
            ]);
        }

        $clientId = session('shopify_oauth_client_id');
        $clientSecret = session('shopify_oauth_client_secret');

        if (!$code || !$clientId || !$clientSecret) {
            return redirect('/integrations/shopify')->with('status', [
                'type' => 'error',
                'message' => __('Missing OAuth parameters. Please try again.'),
            ]);
        }

        // Exchange authorization code for access token
        $response = Http::post("https://{$shopDomain}/admin/oauth/access_token", [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'code' => $code,
        ]);

        if (!$response->successful()) {
            return redirect('/integrations/shopify')->with('status', [
                'type' => 'error',
                'message' => __('Failed to get access token from Shopify: ') . ($response->json('error_description') ?? $response->body()),
            ]);
        }

        $accessToken = $response->json('access_token');
        $scope = $response->json('scope');
        $organizationId = session()->get('current_organization');

        // Create or update the store
        $store = ShopifyStore::updateOrCreate(
            [
                'organization_id' => $organizationId,
                'shop_domain' => $shopDomain,
            ],
            [
                'api_key' => $clientId,
                'api_secret' => $clientSecret,
                'access_token' => $accessToken,
                'is_active' => true,
                'enabled_events' => config('shopify.webhook_events'),
                'metadata' => ['scope' => $scope],
            ]
        );

        // Create default cart recovery settings if they don't exist
        ShopifyCartRecoverySetting::firstOrCreate(
            [
                'shopify_store_id' => $store->id,
            ],
            [
                'organization_id' => $organizationId,
                'is_active' => false,
                'reminder_1_delay_minutes' => config('shopify.cart_recovery.reminder_1_delay'),
                'reminder_2_delay_minutes' => config('shopify.cart_recovery.reminder_2_delay'),
                'reminder_3_delay_minutes' => config('shopify.cart_recovery.reminder_3_delay'),
            ]
        );

        // Clear OAuth session data
        session()->forget([
            'shopify_oauth_state',
            'shopify_oauth_shop',
            'shopify_oauth_client_id',
            'shopify_oauth_client_secret',
        ]);

        return redirect('/integrations/shopify')->with('status', [
            'type' => 'success',
            'message' => __('Shopify store connected successfully via OAuth!'),
            'webhook_url' => url("/shopify/webhook/{$store->uuid}"),
        ]);
    }

    /**
     * Store a new Shopify connection (manual method).
     */
    public function storeConnection(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shop_domain' => 'required|string|max:255',
            'api_key' => 'nullable|string|max:255',
            'api_secret' => 'nullable|string|max:500',
            'access_token' => 'required|string|max:500',
            'webhook_secret' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $organizationId = session()->get('current_organization');

        $store = ShopifyStore::create([
            'organization_id' => $organizationId,
            'shop_domain' => $request->shop_domain,
            'api_key' => $request->api_key,
            'api_secret' => $request->api_secret,
            'access_token' => $request->access_token,
            'webhook_secret' => $request->webhook_secret,
            'is_active' => true,
            'enabled_events' => config('shopify.webhook_events'),
        ]);

        // Create default cart recovery settings
        ShopifyCartRecoverySetting::create([
            'organization_id' => $organizationId,
            'shopify_store_id' => $store->id,
            'is_active' => false,
            'reminder_1_delay_minutes' => config('shopify.cart_recovery.reminder_1_delay'),
            'reminder_2_delay_minutes' => config('shopify.cart_recovery.reminder_2_delay'),
            'reminder_3_delay_minutes' => config('shopify.cart_recovery.reminder_3_delay'),
        ]);

        return back()->with('status', [
            'type' => 'success',
            'message' => __('Shopify store connected successfully!'),
            'webhook_url' => url("/shopify/webhook/{$store->uuid}"),
        ]);
    }

    /**
     * Update a Shopify store connection.
     */
    public function updateConnection(Request $request, string $uuid)
    {
        $organizationId = session()->get('current_organization');
        $store = ShopifyStore::where('uuid', $uuid)
            ->where('organization_id', $organizationId)
            ->firstOrFail();

        $validator = Validator::make($request->all(), [
            'shop_domain' => 'sometimes|string|max:255',
            'api_key' => 'nullable|string|max:255',
            'api_secret' => 'nullable|string|max:500',
            'access_token' => 'sometimes|string|max:500',
            'webhook_secret' => 'nullable|string|max:255',
            'is_active' => 'sometimes|boolean',
            'enabled_events' => 'sometimes|array',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $store->update($request->only([
            'shop_domain', 'api_key', 'api_secret', 'access_token',
            'webhook_secret', 'is_active', 'enabled_events',
        ]));

        return back()->with('status', [
            'type' => 'success',
            'message' => __('Store settings updated successfully!'),
        ]);
    }

    /**
     * Delete a Shopify store connection.
     */
    public function deleteConnection(string $uuid)
    {
        $organizationId = session()->get('current_organization');
        $store = ShopifyStore::where('uuid', $uuid)
            ->where('organization_id', $organizationId)
            ->firstOrFail();

        $store->delete();

        return back()->with('status', [
            'type' => 'success',
            'message' => __('Store disconnected successfully!'),
        ]);
    }

    /**
     * Save notification template mappings for a store.
     */
    public function saveNotificationTemplates(Request $request, string $storeUuid)
    {
        $organizationId = session()->get('current_organization');
        $store = ShopifyStore::where('uuid', $storeUuid)
            ->where('organization_id', $organizationId)
            ->firstOrFail();

        $validator = Validator::make($request->all(), [
            'templates' => 'required|array',
            'templates.*.event_type' => 'required|string|in:' . implode(',', ShopifyNotificationTemplate::EVENT_TYPES),
            'templates.*.template_id' => 'nullable|integer|exists:templates,id',
            'templates.*.template_params' => 'nullable|array',
            'templates.*.is_active' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        foreach ($request->templates as $tmpl) {
            ShopifyNotificationTemplate::updateOrCreate(
                [
                    'shopify_store_id' => $store->id,
                    'event_type' => $tmpl['event_type'],
                ],
                [
                    'organization_id' => $organizationId,
                    'template_id' => $tmpl['template_id'],
                    'template_params' => $tmpl['template_params'] ?? [],
                    'is_active' => $tmpl['is_active'],
                ]
            );
        }

        return back()->with('status', [
            'type' => 'success',
            'message' => __('Notification templates saved successfully!'),
        ]);
    }

    /**
     * Get notification template settings for a store.
     */
    public function getNotificationTemplates(string $storeUuid)
    {
        $organizationId = session()->get('current_organization');
        $store = ShopifyStore::where('uuid', $storeUuid)
            ->where('organization_id', $organizationId)
            ->firstOrFail();

        $templates = ShopifyNotificationTemplate::where('shopify_store_id', $store->id)
            ->with('template:id,name,language,category')
            ->get();

        return response()->json(['templates' => $templates]);
    }

    /**
     * Save cart recovery settings for a store.
     */
    public function saveCartRecoverySettings(Request $request, string $storeUuid)
    {
        $organizationId = session()->get('current_organization');
        $store = ShopifyStore::where('uuid', $storeUuid)
            ->where('organization_id', $organizationId)
            ->firstOrFail();

        $validator = Validator::make($request->all(), [
            'is_active' => 'required|boolean',
            'reminder_1_enabled' => 'required|boolean',
            'reminder_1_delay_minutes' => 'required|integer|min:1',
            'reminder_1_template_id' => 'nullable|integer|exists:templates,id',
            'reminder_1_params' => 'nullable|array',
            'reminder_2_enabled' => 'required|boolean',
            'reminder_2_delay_minutes' => 'required|integer|min:1',
            'reminder_2_template_id' => 'nullable|integer|exists:templates,id',
            'reminder_2_params' => 'nullable|array',
            'reminder_3_enabled' => 'required|boolean',
            'reminder_3_delay_minutes' => 'required|integer|min:1',
            'reminder_3_template_id' => 'nullable|integer|exists:templates,id',
            'reminder_3_params' => 'nullable|array',
            'discount_code' => 'nullable|string|max:50',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        ShopifyCartRecoverySetting::updateOrCreate(
            ['shopify_store_id' => $store->id],
            array_merge(
                $request->only([
                    'is_active',
                    'reminder_1_enabled', 'reminder_1_delay_minutes', 'reminder_1_template_id', 'reminder_1_params',
                    'reminder_2_enabled', 'reminder_2_delay_minutes', 'reminder_2_template_id', 'reminder_2_params',
                    'reminder_3_enabled', 'reminder_3_delay_minutes', 'reminder_3_template_id', 'reminder_3_params',
                    'discount_code', 'discount_percentage',
                ]),
                ['organization_id' => $organizationId]
            )
        );

        return back()->with('status', [
            'type' => 'success',
            'message' => __('Cart recovery settings saved successfully!'),
        ]);
    }

    /**
     * Get cart recovery settings.
     */
    public function getCartRecoverySettings(string $storeUuid)
    {
        $organizationId = session()->get('current_organization');
        $store = ShopifyStore::where('uuid', $storeUuid)
            ->where('organization_id', $organizationId)
            ->firstOrFail();

        $settings = ShopifyCartRecoverySetting::where('shopify_store_id', $store->id)->first();

        return response()->json(['settings' => $settings]);
    }

    /**
     * View abandoned carts for a store.
     */
    public function abandonedCarts(Request $request, string $storeUuid)
    {
        $organizationId = session()->get('current_organization');
        $store = ShopifyStore::where('uuid', $storeUuid)
            ->where('organization_id', $organizationId)
            ->firstOrFail();

        $carts = ShopifyAbandonedCart::where('shopify_store_id', $store->id)
            ->orderBy('abandoned_at', 'desc')
            ->paginate(20);

        return response()->json(['carts' => $carts]);
    }

    /**
     * View notification logs for a store.
     */
    public function notificationLogs(Request $request, string $storeUuid)
    {
        $organizationId = session()->get('current_organization');
        $store = ShopifyStore::where('uuid', $storeUuid)
            ->where('organization_id', $organizationId)
            ->firstOrFail();

        $logs = ShopifyNotificationLog::where('shopify_store_id', $store->id)
            ->with('contact:id,first_name,last_name,phone')
            ->orderBy('created_at', 'desc')
            ->paginate(30);

        return response()->json(['logs' => $logs]);
    }

    /**
     * Get prebuilt template details for preview/edit.
     */
    public function getPrebuiltTemplate(string $templateKey)
    {
        $prebuilt = config("shopify.prebuilt_templates.{$templateKey}");

        if (!$prebuilt) {
            return response()->json(['error' => 'Template not found'], 404);
        }

        // Replace {{STORE_DOMAIN}} placeholder with actual store domain
        $organizationId = session()->get('current_organization');
        $activeStore = ShopifyStore::where('organization_id', $organizationId)
            ->where('is_active', true)
            ->first();
        if ($activeStore && $activeStore->shop_domain) {
            $storeDomain = rtrim($activeStore->shop_domain, '/');
            array_walk_recursive($prebuilt, function (&$value) use ($storeDomain) {
                if (is_string($value)) {
                    $value = str_replace('{{STORE_DOMAIN}}', $storeDomain, $value);
                }
            });
        }

        return response()->json(['template' => $prebuilt]);
    }

    /**
     * Submit a prebuilt (or customized) template to Meta for approval.
     */
    public function submitTemplate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'template_key' => 'required|string',
            'name' => 'required|string|max:512|regex:/^[a-z][a-z0-9_]*$/',
            'category' => 'required|string|in:UTILITY,MARKETING',
            'language' => 'required|string|max:10',
            'header_text' => 'nullable|string|max:60',
            'body_text' => 'required|string|max:1024',
            'footer_text' => 'nullable|string|max:60',
            'buttons' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $organizationId = session()->get('current_organization');
        $organization = Organization::find($organizationId);

        if (!$organization) {
            return response()->json(['success' => false, 'message' => 'Organization not found'], 404);
        }

        $config = json_decode($organization->metadata, true);
        if (empty($config['whatsapp']['access_token']) || empty($config['whatsapp']['waba_id'])) {
            return response()->json([
                'success' => false,
                'message' => __('WhatsApp API not configured. Please set up your WhatsApp credentials first.'),
            ], 422);
        }

        // Check if template with this name already exists
        $existing = Template::where('organization_id', $organizationId)
            ->where('name', $request->name)
            ->where('language', $request->language)
            ->whereNull('deleted_at')
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => __('A template with this name already exists. Status: ') . $existing->status,
                'existing_template' => $existing,
            ], 422);
        }

        // Get prebuilt config for button variable map
        $prebuilt = config("shopify.prebuilt_templates.{$request->template_key}");

        // Build the request data as WhatsappService expects
        $templateRequest = new Request();
        $buttons = $request->buttons ?? ($prebuilt['buttons'] ?? []);

        // Replace {{STORE_DOMAIN}} placeholder in button URLs with the actual store domain
        $store = ShopifyStore::where('organization_id', $organizationId)
            ->where('is_active', true)
            ->first();

        if ($store && $store->shop_domain) {
            $storeDomain = rtrim($store->shop_domain, '/');
            foreach ($buttons as &$btn) {
                if (($btn['type'] ?? '') === 'URL' && isset($btn['url'])) {
                    $btn['url'] = str_replace('{{STORE_DOMAIN}}', $storeDomain, $btn['url']);
                }
            }
            unset($btn);
        }

        $templateRequest->merge([
            'name' => $request->name,
            'category' => $request->category,
            'language' => $request->language,
            'header' => [
                'format' => 'TEXT',
                'text' => $request->header_text,
                'example' => [],
            ],
            'body' => [
                'text' => $request->body_text,
                'example' => $prebuilt ? ($prebuilt['body']['example'] ?? []) : [],
            ],
            'footer' => [
                'text' => $request->footer_text,
            ],
        ]);

        if (!empty($buttons)) {
            $templateRequest->merge(['buttons' => $buttons]);
        }

        try {
            $config = $organization->metadata ? json_decode($organization->metadata, true) : [];
            $accessToken = $config['whatsapp']['access_token'] ?? null;
            $apiVersion = config('graph.api_version');
            $appId = $config['whatsapp']['app_id'] ?? null;
            $phoneNumberId = $config['whatsapp']['phone_number_id'] ?? null;
            $wabaId = $config['whatsapp']['waba_id'] ?? null;

            if (!$accessToken || !$phoneNumberId || !$wabaId) {
                return response()->json([
                    'success' => false,
                    'message' => __('WhatsApp API is not configured. Please set up your WhatsApp credentials first.'),
                ], 422);
            }

            $whatsappService = new WhatsappService($accessToken, $apiVersion, $appId, $phoneNumberId, $wabaId, $organizationId);
            $result = $whatsappService->createTemplate($templateRequest);

            if ($result->success) {
                // Also auto-map this template to the event if a store exists
                $store = ShopifyStore::where('organization_id', $organizationId)
                    ->where('is_active', true)
                    ->first();

                $eventType = $request->template_key;
                // Map cart reminder keys to their base event
                $isCartReminder = str_starts_with($eventType, 'cart_reminder_');

                if ($store && !$isCartReminder && in_array($eventType, ShopifyNotificationTemplate::EVENT_TYPES)) {
                    $template = Template::where('organization_id', $organizationId)
                        ->where('name', $request->name)
                        ->where('language', $request->language)
                        ->whereNull('deleted_at')
                        ->first();

                    if ($template) {
                        ShopifyNotificationTemplate::updateOrCreate(
                            [
                                'shopify_store_id' => $store->id,
                                'event_type' => $eventType,
                            ],
                            [
                                'organization_id' => $organizationId,
                                'template_id' => $template->id,
                                'template_params' => [
                                    'body' => $prebuilt['body']['variable_map'] ?? [],
                                    'buttons' => $prebuilt['button_variable_map'] ?? [],
                                ],
                                'is_active' => true,
                            ]
                        );
                    }
                }

                return response()->json([
                    'success' => true,
                    'message' => __('Template submitted to Meta for approval! Status: ') . ($result->data->status ?? 'PENDING'),
                    'status' => $result->data->status ?? 'PENDING',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result->message ?? __('Failed to submit template to Meta.'),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error: ') . $e->getMessage(),
            ], 500);
        }
    }
}
