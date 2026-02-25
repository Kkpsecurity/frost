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


        $Challenge = Challenge::create([
            'student_lesson_id' => self::$_StudentLesson->id,
            'is_final'          => $flags['is_final'] ?? false,
            'is_eol'            => $flags['is_eol']   ?? false,
            'expires_at'        => Carbon::now()->addSeconds(self::$_config->challenge_expires_at),
        ]);


        RCache::Locker($locker_key, 0);


        // Log challenge_presented activity — non-fatal
        try {
            $studentUnit = self::$_StudentLesson->StudentUnit;
            $courseAuth  = $studentUnit?->CourseAuth;
            $userId      = (int) ($courseAuth?->user_id ?? 0);

            if ($userId > 0) {
                StudentActivity::create([
                    'user_id'         => $userId,
                    'course_auth_id'  => (int) ($studentUnit?->course_auth_id ?? 0),
                    'course_date_id'  => (int) ($studentUnit?->course_date_id ?? 0),
                    'student_unit_id' => (int) ($studentUnit?->id ?? 0),
                    'inst_unit_id'    => (int) ($studentUnit?->inst_unit_id ?? 0),
                    'category'        => StudentActivity::CATEGORY_INTERACTION,
                    'activity_type'   => StudentActivity::TYPE_CHALLENGE_PRESENTED,
                    'description'     => 'Challenge presented'
                        . (($flags['is_final'] ?? false) ? ' (final)' : '')
                        . (($flags['is_eol']   ?? false) ? ' (EOL)'   : ''),
                    'data' => [
                        'challenge_id'      => (int) $Challenge->id,
                        'student_lesson_id' => (int) $Challenge->student_lesson_id,
                        'lesson_id'         => (int) self::$_StudentLesson->lesson_id,
                        'is_final'          => (bool) ($flags['is_final'] ?? false),
                        'is_eol'            => (bool) ($flags['is_eol']   ?? false),
                    ],
                ]);
            }
        } catch (\Throwable $e) {
            // Non-fatal — never break challenge creation
        }


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
