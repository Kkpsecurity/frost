<?php

return [

    'disabled'              => env( 'CHALLENGER_DISBLED', false ),

    'challenge_time'        => 300,  // 5 min
    'challenge_expires_at'  => 335,  // 5m 35s; challenge_time + fudge factor

    // random window
    'lesson_start_min'      => 300,  // 5min
    'lesson_start_max'      => 900,  // 15min

    // random window
    'lesson_random_min'     => 600,  // 10min
    'lesson_random_max'     => 1800, // 30min

    // NOT random
    'final_challenge_min'   => 600,  // 10min
    'final_challenge_max'   => 1200, // 20min ; automatically DNC StudentLesson

];
