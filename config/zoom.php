<?php

return [
    'api_key' => env('ZOOM_CLIENT_KEY'),
    'api_secret' => env('ZOOM_CLIENT_SECRET'),
    'base_url' => env('ZOOM_API_URL'),
    'signature_endpoint' => env('ZOOM_SIGNATURE_ENDPOINT'),
    'token_life' => 60 * 60 * 24 * 7, // 1 week in seconds
    'authentication_method' => 'jwt', // Using JWT for authentication
    'max_api_calls_per_request' => '5',
    'meeting_sdk' => env('ZOOM_MEETING_SDK'),
    'meeting_secret' => env('ZOOM_MEETING_SECRET'),
    'screen_share_url' => 'portal/zoom/screen_share',

    // Zoom Meeting SDK Web credentials
    'sdk_key' => env('ZOOM_SDK_KEY') ?: env('ZOOM_MEETING_SDK'),
    'sdk_secret' => env('ZOOM_SDK_SECRET') ?: env('ZOOM_MEETING_SECRET'),
];
