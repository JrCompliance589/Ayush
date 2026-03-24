<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Shopify Event Types
    |--------------------------------------------------------------------------
    |
    | Webhook event topics that the module listens to from Shopify.
    |
    */
    'webhook_events' => [
        'orders/create',
        'orders/fulfilled',
        'orders/paid',
        'orders/updated',
        'fulfillments/create',
        'fulfillments/update',
        'checkouts/create',
        'checkouts/update',
        'checkouts/delete',
    ],

    /*
    |--------------------------------------------------------------------------
    | Event Type Mappings
    |--------------------------------------------------------------------------
    |
    | Maps Shopify webhook topics to internal notification event types.
    |
    */
    'event_mappings' => [
        'orders/create' => 'order_confirmation',
        'orders/paid' => 'order_confirmation',
        'fulfillments/create' => 'shipping_update',
        'fulfillments/update' => 'delivery_status',
        'checkouts/create' => 'abandoned_cart',
        'checkouts/update' => 'abandoned_cart',
    ],

    /*
    |--------------------------------------------------------------------------
    | Cart Recovery Defaults
    |--------------------------------------------------------------------------
    */
    'cart_recovery' => [
        'reminder_1_delay' => 30,    // minutes
        'reminder_2_delay' => 360,   // minutes (6 hours)
        'reminder_3_delay' => 1440,  // minutes (24 hours)
        'expiry_hours' => 72,        // hours after which abandoned cart expires
    ],

    /*
    |--------------------------------------------------------------------------
    | Shopify API Version
    |--------------------------------------------------------------------------
    */
    'api_version' => '2024-01',

    /*
    |--------------------------------------------------------------------------
    | Available Template Variables
    |--------------------------------------------------------------------------
    |
    | Variables that users can map to WhatsApp template placeholders.
    |
    */
    'template_variables' => [
        'order_confirmation' => [
            '{{order_number}}',
            '{{customer_name}}',
            '{{customer_first_name}}',
            '{{total_price}}',
            '{{currency}}',
            '{{item_count}}',
            '{{items_summary}}',
            '{{order_status_url}}',
            '{{payment_method}}',
        ],
        'shipping_update' => [
            '{{order_number}}',
            '{{customer_name}}',
            '{{customer_first_name}}',
            '{{tracking_number}}',
            '{{tracking_url}}',
            '{{carrier}}',
            '{{estimated_delivery}}',
        ],
        'delivery_status' => [
            '{{order_number}}',
            '{{customer_name}}',
            '{{customer_first_name}}',
            '{{delivery_status}}',
            '{{tracking_url}}',
        ],
        'cod_verification' => [
            '{{order_number}}',
            '{{customer_name}}',
            '{{customer_first_name}}',
            '{{total_price}}',
            '{{currency}}',
            '{{delivery_address}}',
        ],
        'abandoned_cart' => [
            '{{customer_name}}',
            '{{customer_first_name}}',
            '{{cart_total}}',
            '{{currency}}',
            '{{item_count}}',
            '{{items_summary}}',
            '{{recovery_url}}',
            '{{discount_code}}',
            '{{discount_percentage}}',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Pre-built WhatsApp Templates
    |--------------------------------------------------------------------------
    |
    | Ready-to-use templates for each Shopify event. Users can submit these
    | directly to Meta for approval or customize them first.
    |
    */
    'prebuilt_templates' => [
        'order_confirmation' => [
            'name' => 'shopify_order_confirmation',
            'category' => 'UTILITY',
            'language' => 'en_US',
            'header' => [
                'format' => 'TEXT',
                'text' => 'Order Confirmed',
            ],
            'body' => [
                'text' => "Hi {{1}}, thank you for your order!\n\n📦 Order: #{{2}}\n💰 Total: {{3}} {{4}}\n📝 Items: {{5}}\n\nWe'll notify you once your order is shipped. Track your order anytime using the link below.",
                'example' => ['John', '1234', '599.00', 'INR', 'Blue T-Shirt x1'],
                'variable_map' => ['customer_first_name', 'order_number', 'total_price', 'currency', 'items_summary'],
            ],
            'footer' => [
                'text' => 'Thank you for shopping with us!',
            ],
            'buttons' => [
                ['type' => 'URL', 'text' => 'Track Order', 'url' => 'https://{{STORE_DOMAIN}}/{{1}}', 'example' => ['orders/1234']],
            ],
            'button_variable_map' => ['order_status_url'],
        ],
        'shipping_update' => [
            'name' => 'shopify_shipping_update',
            'category' => 'UTILITY',
            'language' => 'en_US',
            'header' => [
                'format' => 'TEXT',
                'text' => 'Your Order Has Been Shipped',
            ],
            'body' => [
                'text' => "Hi {{1}}, great news! Your order #{{2}} has been shipped.\n\n🚚 Carrier: {{3}}\n📋 Tracking: {{4}}\n📅 Est. Delivery: {{5}}\n\nYou can track your shipment using the button below.",
                'example' => ['John', '1234', 'BlueDart', 'AWB123456', '22 Mar 2026'],
                'variable_map' => ['customer_first_name', 'order_number', 'carrier', 'tracking_number', 'estimated_delivery'],
            ],
            'footer' => [
                'text' => 'Happy shopping!',
            ],
            'buttons' => [
                ['type' => 'URL', 'text' => 'Track Shipment', 'url' => 'https://{{STORE_DOMAIN}}/{{1}}', 'example' => ['track/AWB123']],
            ],
            'button_variable_map' => ['tracking_url'],
        ],
        'delivery_status' => [
            'name' => 'shopify_delivery_status',
            'category' => 'UTILITY',
            'language' => 'en_US',
            'header' => [
                'format' => 'TEXT',
                'text' => 'Delivery Update',
            ],
            'body' => [
                'text' => "Hi {{1}}, here's an update on your order #{{2}}.\n\n📦 Status: {{3}}\n\nIf you have any questions, feel free to reply to this message.",
                'example' => ['John', '1234', 'Delivered'],
                'variable_map' => ['customer_first_name', 'order_number', 'delivery_status'],
            ],
            'footer' => [
                'text' => 'We hope you enjoy your purchase!',
            ],
            'buttons' => [],
            'button_variable_map' => [],
        ],
        'cod_verification' => [
            'name' => 'shopify_cod_verification',
            'category' => 'UTILITY',
            'language' => 'en_US',
            'header' => [
                'format' => 'TEXT',
                'text' => 'COD Order Confirmation',
            ],
            'body' => [
                'text' => "Hi {{1}}, we've received your Cash on Delivery order!\n\n📦 Order: #{{2}}\n💰 Amount to pay: {{3}} {{4}}\n📍 Delivery: {{5}}\n\nPlease keep the exact amount ready at the time of delivery. Reply YES to confirm or NO to cancel.",
                'example' => ['John', '1234', '599.00', 'INR', '123 MG Road, Mumbai'],
                'variable_map' => ['customer_first_name', 'order_number', 'total_price', 'currency', 'delivery_address'],
            ],
            'footer' => [
                'text' => 'Cash on Delivery',
            ],
            'buttons' => [
                ['type' => 'QUICK_REPLY', 'text' => 'Confirm'],
                ['type' => 'QUICK_REPLY', 'text' => 'Cancel'],
            ],
            'button_variable_map' => [],
        ],
        'cart_reminder_1' => [
            'name' => 'shopify_cart_reminder_gentle',
            'category' => 'MARKETING',
            'language' => 'en_US',
            'header' => [
                'format' => 'TEXT',
                'text' => 'You left something behind',
            ],
            'body' => [
                'text' => "Hi {{1}}, we noticed you left some items in your cart.\n\n🛍 Items: {{2}}\n💰 Total: {{3}} {{4}}\n\nYour cart is saved and waiting for you. Complete your purchase before items sell out!",
                'example' => ['John', 'Blue T-Shirt x1', '599.00', 'INR'],
                'variable_map' => ['customer_first_name', 'items_summary', 'cart_total', 'currency'],
            ],
            'footer' => [
                'text' => 'Your cart is saved',
            ],
            'buttons' => [
                ['type' => 'URL', 'text' => 'Complete Purchase', 'url' => 'https://{{STORE_DOMAIN}}/{{1}}', 'example' => ['cart/recover/abc']],
            ],
            'button_variable_map' => ['recovery_url'],
        ],
        'cart_reminder_2' => [
            'name' => 'shopify_cart_reminder_urgency',
            'category' => 'MARKETING',
            'language' => 'en_US',
            'header' => [
                'format' => 'TEXT',
                'text' => 'Still thinking about it',
            ],
            'body' => [
                'text' => "Hi {{1}}, your cart with {{2}} item(s) worth {{3}} {{4}} is still waiting!\n\n⚠️ Items are selling fast and we can't guarantee availability much longer.\n\nDon't miss out — complete your order now!",
                'example' => ['John', '2', '1299.00', 'INR'],
                'variable_map' => ['customer_first_name', 'item_count', 'cart_total', 'currency'],
            ],
            'footer' => [
                'text' => 'Limited stock available',
            ],
            'buttons' => [
                ['type' => 'URL', 'text' => 'Shop Now', 'url' => 'https://{{STORE_DOMAIN}}/{{1}}', 'example' => ['cart/recover/abc']],
            ],
            'button_variable_map' => ['recovery_url'],
        ],
        'cart_reminder_3' => [
            'name' => 'shopify_cart_reminder_discount',
            'category' => 'MARKETING',
            'language' => 'en_US',
            'header' => [
                'format' => 'TEXT',
                'text' => 'Special Offer Just For You',
            ],
            'body' => [
                'text' => "Hi {{1}}, here's an exclusive offer to complete your purchase!\n\n🛍 Your Cart: {{2}} {{3}}\n🏷 Use code *{{4}}* for *{{5}}% OFF*!\n\nThis offer expires soon — grab it before it's gone!",
                'example' => ['John', '1299.00', 'INR', 'COMEBACK10', '10'],
                'variable_map' => ['customer_first_name', 'cart_total', 'currency', 'discount_code', 'discount_percentage'],
            ],
            'footer' => [
                'text' => 'Limited time offer',
            ],
            'buttons' => [
                ['type' => 'URL', 'text' => 'Claim Discount', 'url' => 'https://{{STORE_DOMAIN}}/{{1}}', 'example' => ['cart/recover/abc']],
            ],
            'button_variable_map' => ['recovery_url'],
        ],
    ],
];
