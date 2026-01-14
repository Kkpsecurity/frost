<?php

return [
    'idcard_path' => 'validations/idcards',
    'headshots_path' => 'validations/headshots',

    /*
    |--------------------------------------------------------------------------
    | Instructor Classroom Break Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for instructor breaks during live classroom sessions.
    | Defines the number of breaks allowed per day and duration for each break.
    |
    | Break Structure:
    | - Break 1: 15 minutes (first break of the day)
    | - Break 2: 10 minutes (mid-day break)
    | - Break 3: 15 minutes (final break)
    |
    */

    'instructor_breaks' => [
        // Number of breaks allowed per classroom day
        'breaks_allowed_per_day' => env('INSTRUCTOR_BREAKS_ALLOWED', 3),

        // Duration for each break (in minutes) - indexed by break number (1-based)
        // Break 1 = 15 min, Break 2 = 10 min, Break 3 = 15 min
        'break_durations_minutes' => [
            1 => env('INSTRUCTOR_BREAK_1_DURATION', 15),
            2 => env('INSTRUCTOR_BREAK_2_DURATION', 10),
            3 => env('INSTRUCTOR_BREAK_3_DURATION', 15),
        ],

        // Enforce break duration limits (if true, automatically resume after time expires)
        'enforce_duration_limits' => env('INSTRUCTOR_BREAK_ENFORCE_LIMITS', false),

        // Warning before break expires (seconds) we have a sound chime we can use that form the alert 1min and 30 sec alerts
        'warning_before_expiry_seconds' => env('INSTRUCTOR_BREAK_WARNING_SECONDS', 60),

        // Auto-resume grace period (seconds) - buffer before forced resume
        'auto_resume_grace_period' => env('INSTRUCTOR_BREAK_GRACE_PERIOD', 30),

        // Auto-resume enabled (if true, breaks will auto-resume after duration + grace period)
        'auto_resume' => env('INSTRUCTOR_BREAK_AUTO_RESUME', false),
    ],

];
