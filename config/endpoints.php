<?php

return [
    /*
    |--------------------------------------------------------------------------
    | API Endpoints Configuration
    |--------------------------------------------------------------------------
    |
    | This file manages all API endpoints for the FROST application.
    | Organized by feature/module for easy maintenance and documentation.
    |
    */

    // Main dashboard route (web route)
    'dashboard' => [
        'path' => '/classroom',
        'method' => 'GET',
        'description' => 'Student dashboard - shows purchased courses',
        'auth_required' => true,
        'type' => 'web',
    ],

    // Polling API endpoints (only active in classroom mode)
    'student_polling' => [
        'path' => '/classroom/student/data',
        'method' => 'GET',
        'description' => 'Student data polling endpoint',
        'auth_required' => true,
        'rate_limit' => '60,1',
        'type' => 'api',
        'active_in' => 'classroom_mode',
    ],

    'classroom_polling' => [
        'path' => '/classroom/data',
        'method' => 'GET',
        'description' => 'Classroom data polling endpoint',
        'auth_required' => true,
        'rate_limit' => '60,1',
        'type' => 'api',
        'active_in' => 'classroom_mode',
    ],

    'polling' => [
        'intervals' => [
            'student_data' => 30, // seconds (only in classroom mode)
            'classroom_data' => 45, // seconds (only in classroom mode)
        ],
        'retry_attempts' => 3,
        'timeout' => 10, // seconds
    ],

    'debug' => [
        'base_path' => '/debug',
        'endpoints' => [
            'student_dashboard' => [
                'path' => '/student-dashboard',
                'method' => 'GET',
                'description' => 'Debug endpoint matching Laravel props structure',
                'auth_required' => false,
                'matches_props' => 'student-dashboard-data',
            ],
            'class_dashboard' => [
                'path' => '/class-dashboard', 
                'method' => 'GET',
                'description' => 'Debug endpoint matching Laravel props structure',
                'auth_required' => false,
                'matches_props' => 'class-dashboard-data',
            ]
        ]
    ],

    'middleware' => [
        'api' => ['auth:sanctum', 'throttle:api'],
        'student' => ['auth:sanctum', 'role:student', 'throttle:60,1'],
        'classroom' => ['auth:sanctum', 'role:student|instructor', 'throttle:60,1'],
        'debug' => ['throttle:30,1'], // No auth for debug endpoints
    ]
];
