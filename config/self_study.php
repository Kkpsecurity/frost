<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Self-Study Lesson Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for self-study lesson session management including:
    | - Completion thresholds
    | - Pause time calculations
    | - Quota rounding
    | - Session expiration handling
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Completion Threshold
    |--------------------------------------------------------------------------
    |
    | Percentage of video that must be watched to consider lesson completed.
    | Default: 80% (student must watch 80% of video to pass)
    |
    */

    'completion_threshold' => env('SELF_STUDY_COMPLETION_THRESHOLD', 80),

    /*
    |--------------------------------------------------------------------------
    | Session Buffer Time
    |--------------------------------------------------------------------------
    |
    | Additional time (in minutes) added to lesson length for session duration.
    | This provides buffer for pauses, interruptions, and completing the lesson.
    | 
    | Example: 2-hour video + 15 min buffer = 135 min total session time
    |
    */

    'session_buffer_minutes' => env('SELF_STUDY_SESSION_BUFFER', 15),

    /*
    |--------------------------------------------------------------------------
    | Pause Time Calculation
    |--------------------------------------------------------------------------
    |
    | Algorithm for calculating allowed pause time based on video duration.
    | Promotes continuous engagement while allowing reasonable breaks.
    |
    */

    'pause_time' => [
        // Base pause minutes per hour of video
        // Example: 10 min/hour means 6-hour video = 60 minutes pause
        'minutes_per_hour' => env('SELF_STUDY_PAUSE_PER_HOUR', 10),
        
        // Maximum total pause time (minutes) regardless of video length
        'max_total_minutes' => env('SELF_STUDY_MAX_PAUSE', 60),
        
        // Minimum pause interval (minutes)
        // Smaller intervals won't be included in distribution
        'min_interval_minutes' => 5,
        
        // Preferred pause intervals for distribution (minutes)
        // Example: 30 min total might be distributed as [15, 10, 5]
        'interval_distribution' => [5, 10, 15],
    ],

    /*
    |--------------------------------------------------------------------------
    | Quota Rounding Increments
    |--------------------------------------------------------------------------
    |
    | Standard increments for rounding up quota consumption.
    | Prevents inflated session durations from pause time.
    |
    | Example: 
    | - 12 minutes watched → rounds to 15 minutes
    | - 18 minutes watched → rounds to 30 minutes
    | - 45 minutes watched → rounds to 60 minutes
    |
    */

    'quota_rounding_increments' => [15, 30, 60], // minutes

    /*
    |--------------------------------------------------------------------------
    | Session Expiration Handling
    |--------------------------------------------------------------------------
    |
    | Configuration for session timeout and warning behavior.
    |
    */

    'session_expiration' => [
        // Grace period before hard session cutoff (minutes)
        'grace_period_minutes' => 5,
        
        // Show warning N minutes before expiration
        'warning_minutes' => 5,
        
        // Auto-save progress interval (seconds)
        'auto_save_interval_seconds' => 30,
    ],

    /*
    |--------------------------------------------------------------------------
    | Video Player Controls
    |--------------------------------------------------------------------------
    |
    | Configuration for video player behavior and restrictions.
    |
    */

    'video_player' => [
        // Prevent forward seeking (rewind only)
        'allow_forward_seek' => false,
        
        // Allow pause (can be disabled if needed)
        'allow_pause' => true,
        
        // Allow playback speed changes
        'allow_speed_change' => false,
        
        // Allowed playback speeds (if speed change enabled)
        'allowed_speeds' => [0.75, 1.0, 1.25, 1.5],
    ],

    /*
    |--------------------------------------------------------------------------
    | Quota Refund Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for quota refund when online lesson passes after
    | self-study redo.
    |
    */

    'quota_refund' => [
        // Automatically refund quota when online lesson passes
        'auto_refund_enabled' => true,
        
        // Refund full amount or prorated based on usage
        'refund_strategy' => 'full', // 'full' or 'prorated'
        
        // Log refund activities
        'log_refunds' => true,
    ],

];
