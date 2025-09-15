<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Auto-Create Classrooms Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the automated classroom creation system that runs
    | daily at 07:00 AM ET to create classrooms from scheduled course dates.
    |
    */

    // Feature flag to enable/disable auto-creation
    'enabled' => env('AUTO_CLASSROOM_ENABLED', true),

    // Timezone for the daily schedule
    'timezone' => env('AUTO_CLASSROOM_TIMEZONE', 'America/New_York'),

    // Time to run the auto-creation (24-hour format)
    'schedule_time' => env('AUTO_CLASSROOM_SCHEDULE_TIME', '07:00'),

    // Notification settings
    'notifications' => [
        'enabled' => env('AUTO_CLASSROOM_NOTIFICATIONS_ENABLED', true),
        'instructor_email' => env('AUTO_CLASSROOM_INSTRUCTOR_EMAIL', true),
        'instructor_sms' => env('AUTO_CLASSROOM_INSTRUCTOR_SMS', false),
        'admin_alerts' => env('AUTO_CLASSROOM_ADMIN_ALERTS', true),
        'admin_email' => env('AUTO_CLASSROOM_ADMIN_EMAIL', 'admin@frost.local'),
    ],

    // Meeting configuration
    'meeting' => [
        'default_provider' => env('AUTO_CLASSROOM_MEETING_PROVIDER', 'zoom'),
        'online_default_duration_buffer' => 30, // minutes before/after
        'generate_meeting_links' => env('AUTO_CLASSROOM_GENERATE_LINKS', true),
    ],

    // Capacity and policies
    'classroom' => [
        'default_capacity' => env('AUTO_CLASSROOM_DEFAULT_CAPACITY', 30),
        'default_waitlist_policy' => env('AUTO_CLASSROOM_WAITLIST_POLICY', 'none'),
        'late_join_cutoff_minutes' => env('AUTO_CLASSROOM_LATE_JOIN_CUTOFF', 30),
    ],

    // Materials to auto-seed
    'default_materials' => [
        'syllabus' => [
            'title' => 'Course Syllabus',
            'type' => 'syllabus',
            'is_required' => true,
            'sort_order' => 1,
        ],
        'attendance_sheet' => [
            'title' => 'Attendance Sheet',
            'type' => 'attendance_sheet',
            'is_required' => true,
            'sort_order' => 2,
        ],
        'pre_class_checklist' => [
            'title' => 'Pre-Class Checklist',
            'type' => 'checklist',
            'is_required' => true,
            'sort_order' => 3,
        ],
        'required_forms' => [
            'title' => 'Required Forms',
            'type' => 'required_form',
            'is_required' => true,
            'sort_order' => 4,
        ],
    ],

    // Cache settings
    'cache' => [
        'warm_caches' => env('AUTO_CLASSROOM_WARM_CACHES', true),
        'update_search_index' => env('AUTO_CLASSROOM_UPDATE_SEARCH', true),
        'cache_ttl' => env('AUTO_CLASSROOM_CACHE_TTL', 3600), // 1 hour
    ],

    // Retry and error handling
    'retry' => [
        'max_attempts' => env('AUTO_CLASSROOM_MAX_ATTEMPTS', 3),
        'retry_delay_seconds' => env('AUTO_CLASSROOM_RETRY_DELAY', 30),
        'fail_on_missing_instructor' => env('AUTO_CLASSROOM_FAIL_NO_INSTRUCTOR', true),
    ],

    // Logging and metrics
    'logging' => [
        'enabled' => env('AUTO_CLASSROOM_LOGGING_ENABLED', true),
        'level' => env('AUTO_CLASSROOM_LOG_LEVEL', 'info'),
        'structured_logging' => env('AUTO_CLASSROOM_STRUCTURED_LOGS', true),
        'metrics_enabled' => env('AUTO_CLASSROOM_METRICS_ENABLED', true),
    ],

    // Safety and validation
    'safety' => [
        'max_classrooms_per_run' => env('AUTO_CLASSROOM_MAX_PER_RUN', 100),
        'require_active_course_unit' => env('AUTO_CLASSROOM_REQUIRE_ACTIVE_UNIT', true),
        'require_instructor_assigned' => env('AUTO_CLASSROOM_REQUIRE_INSTRUCTOR', true),
        'validate_enrollment_capacity' => env('AUTO_CLASSROOM_VALIDATE_CAPACITY', true),
    ],

    // Command options
    'command' => [
        'allow_dry_run' => true,
        'allow_force_recreate' => env('AUTO_CLASSROOM_ALLOW_FORCE', false),
        'require_admin_ack_for_force' => env('AUTO_CLASSROOM_REQUIRE_ADMIN_ACK', true),
    ],

];
