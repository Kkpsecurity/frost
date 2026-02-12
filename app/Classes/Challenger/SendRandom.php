<?php

declare(strict_types=1);

namespace App\Classes\Challenger;

use Illuminate\Support\Carbon;

use App\Classes\ChallengerResponse;
use App\Models\Challenge;


trait SendRandom
{
    protected static $_StudentLesson;
    protected static $_config;

    protected static function _SendRandom(Challenge $Challenge): ?ChallengerResponse
    {

        $debug_tag = "_SendRandom(CH:{$Challenge->id})";


        //
        // Instructor Lesson is paused
        //

        if (self::$_StudentLesson->InstLesson->is_paused) {
            kkpdebug('Challenger_Dbg', "{$debug_tag} InstLesson->is_paused");
            return null;
        }


        // Check history: count completed/failed challenges for this lesson
        $historyCount = Challenge::where('student_lesson_id', self::$_StudentLesson->id)
            ->where('id', '<=', $Challenge->id)
            ->where(function ($q) {
                $q->whereNotNull('completed_at')
                    ->orWhereNotNull('failed_at');
            })
            ->count();

        // Use completed_at or failed_at as reference time (timing starts from when resolved)
        $referenceTime = $Challenge->completed_at ?? $Challenge->failed_at ?? $Challenge->created_at;
        $CreatedAt = Carbon::parse($referenceTime);

        // Determine timing based on history
        if ($historyCount >= 2) {
            // With History (2+ completed): 10-20 minutes
            $minInterval = self::$_config->lesson_random_min;  // 10 min
            $maxInterval = self::$_config->lesson_random_max;  // 20 min
            kkpdebug('Challenger_Dbg', "{$debug_tag} With history ({$historyCount}): {$minInterval}-{$maxInterval}s");
        } else {
            // No History (< 2 completed): 5-15 minutes
            $minInterval = self::$_config->lesson_no_history_min ?? 300;  // 5 min
            $maxInterval = self::$_config->lesson_no_history_max ?? 900;  // 15 min
            kkpdebug('Challenger_Dbg', "{$debug_tag} No history ({$historyCount}): {$minInterval}-{$maxInterval}s");
        }

        //
        // too soon
        //

        if (Carbon::now()->lt((clone $CreatedAt)->addSeconds($minInterval))) {
            kkpdebug('Challenger_Dbg', "{$debug_tag} [too soon]");
            return null;
        }

        //
        // end of window; send now
        //

        if (Carbon::now()->gt((clone $CreatedAt)->addSeconds($maxInterval))) {
            kkpdebug('Challenger_Msg', "{$debug_tag} Creating Challenge [end of window]");
            return self::_CreateChallenge();
        }

        //
        // random selection
        //

        if (self::_Randomizer(clone $CreatedAt, self::$_config->lesson_random_max)) {
            kkpdebug('Challenger_Msg', "{$debug_tag} Creating Challenge");
            return self::_CreateChallenge();
        }

        kkpdebug('Challenger_Dbg', "{$debug_tag} [not random]");
        return null;
    }
}
