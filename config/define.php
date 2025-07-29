<?php

return [

    'regex' => [
        'base64' => '^[A-Za-z0-9+/]+={0,2}$',
        'uuidv4' => '^[0-9A-Fa-f]{8}-?[0-9A-Fa-f]{4}-?4[0-9A-Fa-f]{3}-?[89AaBb][0-9A-Fa-f]{3}-?[0-9A-Fa-f]{12}$',
    ],

    'email_groups' => [
        'support'  => [
            'richievc@gmail.com'
        ]
    ],

    'carbon_format' => [
        'default'   => 'YYYY-MM-DD HH:mm',
    ],

    'date_formats' => [
        'short_date' => 'm/d/Y',
        'medium_date' => 'M j, Y',
        'long_date' => 'F j, Y',
        'datetime_short' => 'm/d/Y g:i A',
        'datetime_medium' => 'M j, Y g:i A',
        'datetime_long' => 'F j, Y g:i A',
        'time_only' => 'g:i A',
        'date_only' => 'M j, Y',
    ],

    'timezone' => [
        'default' => 'America/New_York',
    ],

    'licenses'  => [
        'STG'   => [
            'DS' => 'DS1600033'  // config( 'define.licenses.STG.DS' )
        ],
    ],

    'student_info' => [
        'suffixes' => ['Jr.', 'Sr.', 'I', 'II', 'III'],
    ],


    //
    // support
    //


    'support' => [
        'manager_user_id' => 5000,
        'max_user_id'     => 9999,
    ],


    //
    // payments
    //


    'payflowpro' => [
        'invoice_ext'   => '-FROST',
        'sandbox_ext'   => '-FROSTSB',
    ],

    #'paypal' => [
    #    'invoice_ext'   => '-FROST',
    #    'sandbox_ext'   => '-FROSTSB',
    #],


];
