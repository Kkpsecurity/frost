<?php

/**
 * Agora config file
 */

return [

    'app_id'        => env( 'AGORA_APP_ID' ),
    'certificate'   => env( 'AGORA_APP_CERTIFICATE' ),

    'rtc' => [
        'endpoint'          => env( 'AGORA_RTCTOKEN_ENDPOINT' ),
        'expire_minutes'    => env( 'AGORA_RTCTOKEN_EXPIRE_MINUTES' ),
    ],

    'rtm' => [
        'endpoint'          => env( 'AGORA_RTMTOKEN_ENDPOINT' ),
        'expire_minutes'    => env( 'AGORA_RTMTOKEN_EXPIRE_MINUTES' ),
    ],

];
