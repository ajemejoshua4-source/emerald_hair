<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Paystack credentials
    |--------------------------------------------------------------------------
    |
    | Create these under https://dashboard.paystack.com/#/settings/developers.
    | When no secret key is present online payment is disabled and the
    | storefront falls back to WhatsApp order confirmation.
    |
    */

    'public' => env('PAYSTACK_PUBLIC_KEY'),

    'secret' => env('PAYSTACK_SECRET_KEY'),

    'merchant_email' => env('PAYSTACK_MERCHANT_EMAIL'),

    'base_url' => env('PAYSTACK_BASE_URL', 'https://api.paystack.co'),

    'callback_url' => env('PAYSTACK_CALLBACK_URL'),

];
