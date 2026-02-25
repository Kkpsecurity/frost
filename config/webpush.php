<?php

return [
    /*
    |--------------------------------------------------------------------------
    | VAPID Keys for Web Push Notifications
    |--------------------------------------------------------------------------
    |
    | These keys are used to authenticate your server when sending push
    | notifications.  Generate once with:
    |
    |   php artisan webpush:vapid
    |   (or via VAPID::createVapidKeys() in tinker)
    |
    | IMPORTANT: Never rotate keys without first deleting ALL existing
    | push_subscriptions rows — the old public key is baked into each
    | browser subscription and mismatches cause silent drop.
    |
    */
    'vapid' => [
        'subject'     => env('VAPID_SUBJECT', 'mailto:admin@example.com'),
        'public_key'  => env('VAPID_PUBLIC_KEY', ''),
        'private_key' => env('VAPID_PRIVATE_KEY', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | OpenSSL Configuration (Windows / Laragon fix)
    |--------------------------------------------------------------------------
    |
    | On Windows, PHP CLI may not find openssl.cnf automatically.
    | Set OPENSSL_CONF in .env to the absolute path of your openssl.cnf file:
    |
    |   OPENSSL_CONF=C:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\extras\ssl\openssl.cnf
    |
    | If empty, no override is applied (production Linux servers don't need it).
    |
    */
    'openssl_conf' => env('OPENSSL_CONF', ''),

    /*
    |--------------------------------------------------------------------------
    | TTL for Push Notifications
    |--------------------------------------------------------------------------
    |
    | Maximum time (seconds) the push service should retry delivery.
    | Default: 4 hours (14400). Max: 2419200 (28 days).
    |
    */
    'ttl' => (int) env('WEBPUSH_TTL', 14400),

    /*
    |--------------------------------------------------------------------------
    | Urgency
    |--------------------------------------------------------------------------
    |
    | Controls delivery priority on the push service.
    | Allowed: "very-low", "low", "normal", "high"
    |
    */
    'urgency' => env('WEBPUSH_URGENCY', 'normal'),
];
