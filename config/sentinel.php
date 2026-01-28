<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Sentinel Bridge Enabled
    |--------------------------------------------------------------------------
    |
    | Enable or disable the Sentinel bridge monitoring system.
    | When disabled, no events will be captured or sent to n8n.
    |
    */

    'enabled' => env('SENTINEL_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Debug Mode
    |--------------------------------------------------------------------------
    |
    | Enable debug logging for Sentinel operations.
    | This will log all event captures and n8n communications.
    |
    */

    'debug' => env('SENTINEL_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | n8n Configuration
    |--------------------------------------------------------------------------
    |
    | Connection settings for the n8n workflow automation service.
    |
    */

    'n8n' => [
        'url' => env('SENTINEL_N8N_URL', 'http://localhost:5678'),
        'token' => env('SENTINEL_N8N_TOKEN', ''),
        'timeout' => env('SENTINEL_N8N_TIMEOUT', 10), // seconds
        'verify_ssl' => env('SENTINEL_N8N_VERIFY_SSL', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Event Tracking
    |--------------------------------------------------------------------------
    |
    | Configure which types of events should be tracked.
    |
    */

    'tracking' => [
        'queries' => env('SENTINEL_TRACK_QUERIES', false),
        'api' => env('SENTINEL_TRACK_API', true),
        'events' => env('SENTINEL_TRACK_EVENTS', true),
        'exceptions' => env('SENTINEL_TRACK_EXCEPTIONS', true),
        'slow_queries' => env('SENTINEL_TRACK_SLOW_QUERIES', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Thresholds
    |--------------------------------------------------------------------------
    |
    | Define thresholds for performance monitoring.
    |
    */

    'thresholds' => [
        'slow_query' => env('SENTINEL_SLOW_QUERY_THRESHOLD', 1000), // milliseconds
        'slow_request' => env('SENTINEL_SLOW_REQUEST_THRESHOLD', 3000), // milliseconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Retry Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for retrying failed webhook deliveries to n8n.
    |
    */

    'retry' => [
        'attempts' => env('SENTINEL_RETRY_ATTEMPTS', 3),
        'delay' => env('SENTINEL_RETRY_DELAY', 5), // seconds
        'multiplier' => 2, // exponential backoff
    ],

    /*
    |--------------------------------------------------------------------------
    | Event Filtering
    |--------------------------------------------------------------------------
    |
    | Filter which events should be sent to n8n based on patterns.
    |
    */

    'filters' => [
        // Only send events matching these patterns
        'include' => [
            'student.*',
            'classroom.*',
            'api.error',
            'zoom.*',
            'health.*',
        ],

        // Never send events matching these patterns
        'exclude' => [
            'heartbeat',
            'ping',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Data Sanitization
    |--------------------------------------------------------------------------
    |
    | Fields to sanitize or exclude from event data before sending to n8n.
    |
    */

    'sanitize' => [
        'remove_fields' => [
            'password',
            'password_confirmation',
            'token',
            'api_key',
            'secret',
            'credit_card',
            'ssn',
        ],
        'mask_fields' => [
            'email' => true,
            'phone' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Event Severity Levels
    |--------------------------------------------------------------------------
    |
    | Map event types to severity levels for prioritization.
    |
    */

    'severity' => [
        'critical' => [
            'api.error.500',
            'database.connection.failed',
            'zoom.complete_failure',
        ],
        'error' => [
            'api.error.4xx',
            'zoom.connection.failed',
            'classroom.session.failed',
        ],
        'warning' => [
            'api.slow_response',
            'database.slow_query',
            'student.onboarding.stuck',
        ],
        'info' => [
            'student.onboarding.complete',
            'classroom.session.started',
            'health.check.passed',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhooks
    |--------------------------------------------------------------------------
    |
    | Define n8n webhook endpoints for different event types.
    |
    */

    'webhooks' => [
        'student_onboarding' => env('SENTINEL_WEBHOOK_STUDENT', '/webhook/student-onboarding'),
        'classroom_session' => env('SENTINEL_WEBHOOK_CLASSROOM', '/webhook/classroom-session'),
        'api_health' => env('SENTINEL_WEBHOOK_HEALTH', '/webhook/api-health'),
        'zoom_integration' => env('SENTINEL_WEBHOOK_ZOOM', '/webhook/zoom-integration'),
        'database_query' => env('SENTINEL_WEBHOOK_DATABASE', '/webhook/database-query'),
        'generic' => env('SENTINEL_WEBHOOK_GENERIC', '/webhook/sentinel-event'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue Configuration
    |--------------------------------------------------------------------------
    |
    | Process Sentinel events asynchronously via Laravel queues.
    |
    */

    'queue' => [
        'enabled' => env('SENTINEL_QUEUE_ENABLED', true),
        'connection' => env('SENTINEL_QUEUE_CONNECTION', 'database'),
        'name' => env('SENTINEL_QUEUE_NAME', 'sentinel'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Storage Configuration
    |--------------------------------------------------------------------------
    |
    | Configure how long to keep sentinel events in the database.
    |
    */

    'storage' => [
        'retention_days' => env('SENTINEL_RETENTION_DAYS', 30),
        'cleanup_enabled' => env('SENTINEL_CLEANUP_ENABLED', true),
        'cleanup_schedule' => '0 2 * * *', // Daily at 2 AM
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Prevent event flooding by rate limiting.
    |
    */

    'rate_limit' => [
        'enabled' => env('SENTINEL_RATE_LIMIT_ENABLED', true),
        'max_per_minute' => env('SENTINEL_RATE_LIMIT_PER_MINUTE', 60),
        'burst' => env('SENTINEL_RATE_LIMIT_BURST', 10),
    ],

    /*
    |--------------------------------------------------------------------------
    | Health Check Configuration
    |--------------------------------------------------------------------------
    |
    | Configure health check monitoring.
    |
    */

    'health' => [
        'enabled' => env('SENTINEL_HEALTH_ENABLED', true),
        'interval' => env('SENTINEL_HEALTH_INTERVAL', 300), // seconds (5 minutes)
        'checks' => [
            'database' => true,
            'redis' => false,
            'queue' => true,
            'storage' => true,
            'n8n' => true,
        ],
    ],

];
