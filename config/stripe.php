<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Stripe Publishable Key
    |--------------------------------------------------------------------------
    | Used in the frontend to identify your Stripe account.
    */
    'key' => env('STRIPE_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Stripe Secret Key
    |--------------------------------------------------------------------------
    | Used server-side to authenticate Stripe API requests.
    */
    'secret' => env('STRIPE_SECRET', ''),

    /*
    |--------------------------------------------------------------------------
    | Stripe Webhook Secret
    |--------------------------------------------------------------------------
    | Used to verify incoming webhook payloads from Stripe.
    | Obtain this from: stripe listen --print-secret  (Stripe CLI)
    */
    'webhook_secret' => env('STRIPE_WEBHOOK_SECRET', ''),

    /*
    |--------------------------------------------------------------------------
    | Default Currency
    |--------------------------------------------------------------------------
    | Stripe uses ISO 4217 currency codes. Use USD for test mode.
    | Change to BDT only if your Stripe account supports it.
    */
    'currency' => env('STRIPE_CURRENCY', 'USD'),

    /*
    |--------------------------------------------------------------------------
    | BDT to USD Exchange Rate
    |--------------------------------------------------------------------------
    | The conversion rate used to convert BDT amounts to USD.
    */
    'exchange_rate' => env('STRIPE_EXCHANGE_RATE', 0.0084),
];
