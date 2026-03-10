<?php

declare(strict_types=1);

namespace App\Classes\Students\Challenger;

use Illuminate\Support\Carbon;

use App\Classes\Students\ChallengerResponse;

use App\Services\RCache;
use App\Models\Challenge;
use App\Models\StudentActivity;
use App\Models\StudentLesson;

use App\Helpers\kkpdebug;

trait CreateChallenge
{
    protected static function _CreateChallenge(?array $flags = null): ?ChallengerResponse
    {

        $debug_tag  = '_CreateChallenge(SL:' . self::$_StudentLesson->id . ')';

        $locker_key = 'CreateChallenge:' . self::$_StudentLesson->id;


        if (! RCache::Locker($locker_key, 5)) {
            kkpdebug('Challenger_ERR', "{$debug_tag} failed to get lock");
            return null;
        }


        $isFinal = (bool) ($flags['is_final'] ?? false);
        $isEol   = (bool) ($flags['is_eol']   ?? false);

        // Rate limit regular challenges (non-final, non-EOL) based on lesson duration.
        if (! $isFinal && ! $isEol) {

            // Rolling window cap: no more than N regular challenges in any 60-minute window.
            $perHour = (int) (self::$_config->challenges_per_hour ?? 0);
            if ($perHour > 0) {
                $recentRegular = Challenge::query()
                    ->where('student_lesson_id', self::$_StudentLesson->id)
                    ->where('is_final', false)
                    ->where('is_eol', false)
                    ->where('created_at', '>=', Carbon::now()->subHour())
                    ->count();

                if ($recentRegular >= $perHour) {
                    kkpdebug('Challenger_Dbg', "{$debug_tag} regular challenge hourly cap reached ({$recentRegular}/{$perHour})");
                    RCache::Locker($locker_key, 0);
                    return null;
                }
            }

            $maxRegular = self::_MaxRegularChallenges();
            $existingRegular = Challenge::query()
                ->where('student_lesson_id', self::$_StudentLesson->id)
                ->where('is_final', false)
                ->where('is_eol', false)
                ->count();

            if ($existingRegular >= $maxRegular) {
                kkpdebug('Challenger_Dbg', "{$debug_tag} max regular challenges reached ({$existingRegular}/{$maxRegular})");
                RCache::Locker($locker_key, 0);
                return null;
            }
        }


        $Challenge = Challenge::create([
            'student_lesson_id' => self::$_StudentLesson->id,
            'is_final'          => $isFinal,
            'is_eol'            => $isEol,
            'expires_at'        => Carbon::now()->addSeconds(self::$_config->challenge_expires_at),
        ]);


        RCache::Locker($locker_key, 0);

        // Log challenge_presented activity (non-fatal)
        self::_LogChallengeActivity(
            $Challenge,
            StudentActivity::TYPE_CHALLENGE_PRESENTED,
            'Challenge presented'
                . ($isFinal ? ' (final)' : '')
                . ($isEol ? ' (EOL)' : '')
        );


        kkpdebug(
            'Challenger_Msg',
            "{$debug_tag} created"
                . (($flags['is_final'] ?? false) ? ' FINAL' : '')
                . (($flags['is_eol']   ?? false) ? ' EOL'   : '')
                . " CH:{$Challenge->id}"
        );


        return self::$_ChallengerResponse->SetChallenge($Challenge);
    }


    protected static function _SetChallengeEOL(Challenge $Challenge): ChallengerResponse
    {

        //
        // don't update expires_at
        //

        if (! $Challenge->is_eol) {

            $Challenge->update([
                'is_eol'     => true,
                'expires_at' => Carbon::now()->addSeconds(self::$_config->challenge_expires_at),
            ]);

            kkpdebug('Challenger_Msg', "_SetChallengeEOL() updated CH:{$Challenge->id}");
        } else {
            kkpdebug('Challenger_Dbg', "_SetChallengeEOL() Challenge already is_eol");
        }

        return self::$_ChallengerResponse->SetChallenge($Challenge);
    }
}
