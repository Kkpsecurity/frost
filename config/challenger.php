<?php

return [

    'disabled'              => env('CHALLENGER_DISABLED', env('CHALLENGER_DISBLED', false)),

    // Optional: speed up timing windows for local testing.
    // When false, Challenger always uses the normal timing windows below.
    'dev_mode'              => env('CHALLENGER_DEV_MODE', false),

    // Dev-mode timing overrides (seconds)
    'dev_lesson_start_min'      => (int) env('CHALLENGER_DEV_LESSON_START_MIN', 30),
    'dev_lesson_start_max'      => (int) env('CHALLENGER_DEV_LESSON_START_MAX', 120),
    'dev_lesson_random_min'     => (int) env('CHALLENGER_DEV_LESSON_RANDOM_MIN', 60),
    'dev_lesson_random_max'     => (int) env('CHALLENGER_DEV_LESSON_RANDOM_MAX', 180),
    'dev_final_challenge_min'   => (int) env('CHALLENGER_DEV_FINAL_CHALLENGE_MIN', 90),
    'dev_final_challenge_max'   => (int) env('CHALLENGER_DEV_FINAL_CHALLENGE_MAX', 240),

    'challenge_time'        => (int) env('CHALLENGER_CHALLENGE_TIME', 180),         // default: 3 min
    'challenge_expires_at'  => (int) env('CHALLENGER_CHALLENGE_EXPIRES_AT', 215),    // default: 3m 35s; challenge_time + fudge factor
    'warning_before_seconds' => (int) env('CHALLENGER_WARNING_BEFORE_SECONDS', 30), // default: 30 sec warning before expiry

    // Rate limit: target number of (non-final, non-EOL) challenges per 60 minutes.
    // Example: 60-minute lesson @ 6/hour => max 6 regular challenges.
    'challenges_per_hour'   => 6,

    // random window
    'lesson_start_min'      => 300,  // 5min
    'lesson_start_max'      => 900,  // 15min

    // random window
    'lesson_random_min'     => 600,  // 10min (with history: 2+ challenges)
    'lesson_random_max'     => 1200, // 20min (with history: 2+ challenges)

    // No history timing (fewer than 2 completed challenges)
    'lesson_no_history_min' => 300,  // 5min
    'lesson_no_history_max' => 900,  // 15min

    // NOT random
    'final_challenge_min'   => 600,  // 10min
    'final_challenge_max'   => 1200, // 20min ; automatically DNC StudentLesson

];
