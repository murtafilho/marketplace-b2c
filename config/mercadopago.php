<?php

return [
    /*
    |--------------------------------------------------------------------------
    | MercadoPago Configuration
    |--------------------------------------------------------------------------
    */
    
    'public_key' => env('MP_PUBLIC_KEY'),
    'access_token' => env('MP_ACCESS_TOKEN'),
    'app_id' => env('MP_APP_ID'),
    'redirect_uri' => env('MP_REDIRECT_URI'),
    'webhook_secret' => env('MP_WEBHOOK_SECRET'),
    'app_fee' => (float) env('MP_APP_FEE', 10.0),
    
    /*
    |--------------------------------------------------------------------------
    | Marketplace Configuration
    |--------------------------------------------------------------------------
    */
    
    'marketplace_commission' => (float) env('MARKETPLACE_COMMISSION', 10.0),
    'seller_auto_approve' => env('SELLER_AUTO_APPROVE', false),
    'product_auto_approve' => env('PRODUCT_AUTO_APPROVE', false),
    
    /*
    |--------------------------------------------------------------------------
    | Webhook Configuration
    |--------------------------------------------------------------------------
    */
    
    'webhook_events' => [
        'payment' => '/webhooks/mercadopago/payment',
        'merchant_order' => '/webhooks/mercadopago/merchant_order',
    ],
];