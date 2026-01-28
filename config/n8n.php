<?php

return [

    /*
    |--------------------------------------------------------------------------
    | n8n Connection Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for connecting to the n8n workflow automation service.
    |
    */

    'url' => env('N8N_URL', 'http://localhost:5678'),
    'username' => env('N8N_USERNAME', 'admin'),
    'password' => env('N8N_PASSWORD', ''),
    'timeout' => env('N8N_TIMEOUT', 30),

    /*
    |--------------------------------------------------------------------------
    | Webhook Endpoints
    |--------------------------------------------------------------------------
    |
    | Predefined webhook endpoints for different workflows.
    |
    */

    'webhooks' => [
        'student_onboarding' => '/webhook/frost/student-onboarding',
        'classroom_session' => '/webhook/frost/classroom-session',
        'api_health' => '/webhook/frost/api-health',
        'zoom_integration' => '/webhook/frost/zoom-integration',
        'database_query' => '/webhook/frost/database-query',
        'backup_notification' => '/webhook/frost/backup-notification',
    ],

    /*
    |--------------------------------------------------------------------------
    | Workflow IDs
    |--------------------------------------------------------------------------
    |
    | n8n workflow IDs for direct execution (optional).
    |
    */

    'workflows' => [
        'student_alert' => env('N8N_WORKFLOW_STUDENT_ALERT'),
        'health_monitor' => env('N8N_WORKFLOW_HEALTH_MONITOR'),
        'zoom_recovery' => env('N8N_WORKFLOW_ZOOM_RECOVERY'),
        'backup_notify' => env('N8N_WORKFLOW_BACKUP_NOTIFY'),
    ],

];
