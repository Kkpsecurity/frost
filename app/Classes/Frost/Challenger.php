<?php

declare(strict_types=1);

namespace App\Classes\Frost;

/**
 * @file Challenger.php
 * @brief Class for handling challenges in the Frost application.
 * @details This class provides methods to manage challenges, including sending, validating, and marking them as completed or failed.
 *
 * @deprecated This is a duplicate/legacy copy. Use App\Classes\Challenger instead.
 * @see \App\Classes\Challenger
 */

use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

use RCache;
use App\Models\Challenge;
use App\Models\StudentActivity;
use App\Models\StudentLesson;
// use App\Classes\Challenger\TraitLoader; // Trait not found - commented out
use App\Classes\Frost\ChallengerResponse;
use App\Traits\AssertConfigTrait; // Fixed namespace
use Illuminate\Support\Facades\Log;


class Challenger
{

    // use TraitLoader; // Trait not found - commented out
    use AssertConfigTrait;


    protected static $_config;
    protected static $_ChallengerResponse;
    protected static $_StudentLesson;


    public static function init(int|StudentLesson|null $StudentLesson = null): self
    {

        if (is_object(self::$_config)) {
            return new self();
        }


        self::$_config = self::AssertConfig('challenger', [
            'challenge_time',
            'challenge_expires_at',
            'lesson_start_min',
            'lesson_start_max',
            'lesson_random_min',
            'lesson_random_max',
            'final_challenge_min',
            'final_challenge_max',
        ]);

        self::_DevelMode();


        self::$_ChallengerResponse = new ChallengerResponse(self::$_config->challenge_time);


        if ($StudentLesson) {
            if (is_int($StudentLesson)) {
                self::$_StudentLesson = StudentLesson::firstOrFail($StudentLesson);
            } else {
                self::$_StudentLesson = $StudentLesson;
            }
        }


        return new self();
    }


    public static function Ready(int|StudentLesson $StudentLesson, array $completed_lesson_ids): ?ChallengerResponse
    {

        self::init($StudentLesson);

        $debug_tag = 'Ready(SL:' . self::$_StudentLesson->id . ')';


        // TODO: remove this
        if (config('challenger.disabled') === true) {
            #kkpdebug( 'Challenger_Dbg', "{$debug_tag} *disabled*" );
            return null;
        }


        //
        // Lesson already completed
        //

        if (in_array($StudentLesson->lesson_id, $completed_lesson_ids)) {
            kkpdebug('Challenger_Msg', "{$debug_tag} LessonID {$StudentLesson->lesson_id} previously completed");
            return null;
        }


        //
        // StudentLesson already complete / DNC
        //

        if (self::$_StudentLesson->completed_at) {
            kkpdebug('Challenger_Dbg', "{$debug_tag} StudentLesson->completed_at");
            return null;
        }

        if (self::$_StudentLesson->dnc_at) {

            kkpdebug('Challenger_Dbg', "{$debug_tag} StudentLesson->dnc_at");
            return null;
        }


        //
        //
        //


        // Active (pending) challenge — neither completed_at nor failed_at is set
        $LatestChallenge = self::$_StudentLesson->LatestChallenge;


        //
        // There is an active (pending) challenge — return it
        //

        if ($LatestChallenge) {

            if (self::_SendCurrent($LatestChallenge)) // bool
            {
                kkpdebug('Challenger_Msg', "{$debug_tag} sending Current (CH:{$LatestChallenge->id})");
                return self::$_ChallengerResponse;
            }

            return null;
        }


        //
        // No active challenge — look at the last RESOLVED one (completed or failed)
        //

        $LastChallenge = Challenge::where('student_lesson_id', self::$_StudentLesson->id)
            ->latest()
            ->first();


        //
        // No challenges at all — beginning of Lesson
        //

        if (! $LastChallenge) {
            return self::_SendFirst(); // ?ChallengerResponse
        }


        //
        // Last challenge was failed (non-final) — send Final challenge
        //

        if ($LastChallenge->failed_at && ! $LastChallenge->is_final) {
            return self::_SendFinal($LastChallenge); // ?ChallengerResponse
        }


        //
        // Last challenge was completed (or final already sent) — send next random
        //

        return self::_SendRandom($LastChallenge); // ?ChallengerResponse

    }


    public static function EOLReady(int|StudentLesson $StudentLesson, array $completed_lesson_ids): ?ChallengerResponse
    {

        self::init($StudentLesson);

        $debug_tag = 'EOLReady(SL:' . self::$_StudentLesson->id . ')';


        // TODO: remove this
        if (config('challenger.disabled') === true) {
            if (! $StudentLesson->completed_at && $StudentLesson->InstLesson->completed_at) {
                kkpdebug('Challenger_Dbg', "{$debug_tag} *disabled* Marking StudentLesson Completed");
                self::$_StudentLesson->MarkCompleted();
            }

            return null;
        }


        //
        // Lesson already completed
        //

        if (in_array($StudentLesson->lesson_id, $completed_lesson_ids)) {
            kkpdebug('Challenger_Msg', "{$debug_tag} LessonID {$StudentLesson->lesson_id} previously completed");
            return null;
        }


        return self::_SendEOL();
    }


    public static function MarkCompleted(int|Challenge $Challenge): void
    {

        self::init();

        if (is_int($Challenge)) {
            $Challenge = Challenge::findOrFail($Challenge);
        }


        $debug_tag = "MarkCompleted(CH:{$Challenge->id})";


        if (! self::_ValidateChallenge($Challenge)) {
            kkpdebug('Challenger_Msg', "{$debug_tag} *** Marking Failed (failed validation) ***");
            self::MarkFailed($Challenge);
            return;
        }


        kkpdebug('Challenger_Msg', $debug_tag);
        $Challenge->MarkCompleted();
        self::_LogChallengeActivity($Challenge, StudentActivity::TYPE_CHALLENGE_COMPLETED, 'Challenge completed');


        //
        // EOL Challenge; give Student credit
        //

        if ($Challenge->is_eol) {
            kkpdebug('Challenger_Msg', "{$debug_tag} Challenge->is_eol :: Marking StudentLesson completed");
            $Challenge->StudentLesson->MarkCompleted();
            return;
        }


        //
        // ! EOL but Instructor closed the Lesson; give Student credit
        //

        if ($Challenge->StudentLesson->InstLesson->completed_at) {
            kkpdebug('Challenger_Msg', "{$debug_tag} *** InstLesson was marked completed *** Marking StudentLesson completed");
            $Challenge->StudentLesson->MarkCompleted();
            return;
        }
    }


    public static function MarkFailed(int|Challenge $Challenge): void
    {

        self::init();

        if (is_int($Challenge)) {
            $Challenge = Challenge::findOrFail($Challenge);
        }


        $debug_tag = "MarkFailed(CH:{$Challenge->id})";


        //
        // sanity checks
        //

        if ($Challenge->completed_at) {
            kkpdebug('Challenger_ERR', "{$debug_tag} Challenge already completed_at");
            return;
        }

        if ($Challenge->failed_at) {
            kkpdebug('Challenger_ERR', "{$debug_tag} Challenge already failed_at");
            return;
        }


        kkpdebug('Challenger_Msg', $debug_tag);
        $Challenge->MarkFailed();
        self::_LogChallengeActivity($Challenge, StudentActivity::TYPE_CHALLENGE_FAILED, 'Challenge failed' . ($Challenge->is_final ? ' (final)' : ($Challenge->is_eol ? ' (EOL)' : '')));


        if ($Challenge->is_final) {

            kkpdebug('Challenger_Msg', "{$debug_tag} Challenge->is_final *** Marking StudentLesson DNC ***");
            $Challenge->StudentLesson->MarkDNC();
        } else if ($Challenge->is_eol) {

            kkpdebug('Challenger_Msg', "{$debug_tag} Challenge->is_eol *** Marking StudentLesson DNC ***");
            $Challenge->StudentLesson->MarkDNC();
        }
    }


    /* REMOVE THIS
    public static function CreateClearedDNC( int $student_lesson_id ) : void
    {

        self::init();

        kkpdebug( 'Challenger_Msg', "CreateClearedDNC() : creating Challenge" );

        $Challenge = Challenge::create([
            'student_lesson_id' => $student_lesson_id,
            'is_final'          => true,
            'expires_at'        => Carbon::now()->addSeconds( self::$_config->challenge_expires_at ),
        ]);

        kkpdebug( 'Challenger_Msg', "CreateClearedDNC() : ChallengeID {$Challenge->id}" );

    }
    */



    ######################
    ###                ###
    ###   randomizer   ###
    ###                ###
    ######################


    protected static function _Randomizer(Carbon $start_timestamp, int $max_seconds): bool
    {

        $minutes = Carbon::now()->diffInMinutes($start_timestamp->addSeconds($max_seconds));

        if ($minutes < 1) {
            return true;
        }

        return (0 == rand(0, $minutes));
    }



    ################################
    ###                          ###
    ###   challenge validation   ###
    ###                          ###
    ################################


    protected static function _ValidateChallenge(Challenge $Challenge): bool
    {

        $debug_tag = "_ValidateChallenge(CH:{$Challenge->id})";

        //
        // Challenge already marked
        //

        if ($Challenge->completed_at) {
            #kkpdebug( 'Challenger_Dbg', "{$debug_tag} Challenge->completed_at" );
            return false;
        }

        if ($Challenge->failed_at) {
            #kkpdebug( 'Challenger_Dbg', "{$debug_tag} Challenge->failed_at" );
            return false;
        }

        //
        // Challenge expired
        //

        if (Carbon::now()->gt(Carbon::parse($Challenge->expires_at))) {
            kkpdebug('Challenger_Msg', "{$debug_tag} Challenge Expired *** Marking Failed ***");
            self::MarkFailed($Challenge);
            return false;
        }

        //
        // Challenge is still valid
        //

        return true;
    }



    private static function _LogChallengeActivity(Challenge $Challenge, string $activityType, string $description): void
    {
        try {
            $studentLesson = $Challenge->StudentLesson;
            $studentUnit   = $studentLesson?->StudentUnit;
            $courseAuth    = $studentUnit?->CourseAuth;
            $userId        = (int) ($courseAuth?->user_id ?? 0);

            if ($userId <= 0) {
                return;
            }

            StudentActivity::create([
                'user_id'         => $userId,
                'course_auth_id'  => (int) ($studentUnit?->course_auth_id ?? 0),
                'course_date_id'  => (int) ($studentUnit?->course_date_id ?? 0),
                'student_unit_id' => (int) ($studentUnit?->id ?? 0),
                'inst_unit_id'    => (int) ($studentUnit?->inst_unit_id ?? 0),
                'category'        => StudentActivity::CATEGORY_INTERACTION,
                'activity_type'   => $activityType,
                'description'     => $description,
                'data' => [
                    'challenge_id'      => (int) $Challenge->id,
                    'student_lesson_id' => (int) $Challenge->student_lesson_id,
                    'lesson_id'         => (int) ($studentLesson?->lesson_id ?? 0),
                    'is_final'          => (bool) $Challenge->is_final,
                    'is_eol'            => (bool) $Challenge->is_eol,
                ],
            ]);
        } catch (\Throwable $e) {
            Log::warning('Challenger (Frost): Failed to log activity', [
                'error'         => $e->getMessage(),
                'activity_type' => $activityType,
                'challenge_id'  => $Challenge->id ?? null,
            ]);
        }
    }


    ###################
    ###             ###
    ###   testing   ###
    ###             ###
    ###################


    protected static function _DevelMode(): void
    {
        if (! app()->environment('production')) {
            self::$_config->lesson_start_min    = 30;
            self::$_config->lesson_start_max    = 90;
            self::$_config->lesson_random_min   = 120; // 2min
            self::$_config->lesson_random_max   = 360; // 6min
            self::$_config->final_challenge_min = 60;  // send it very soon
        }
    }

    protected static function _SendFirst(): ?ChallengerResponse
    {
        // TODO: Implement first challenge logic
        return self::$_ChallengerResponse;
    }

    protected static function _SendFinal(Challenge $LatestChallenge): ?ChallengerResponse
    {
        // TODO: Implement final challenge logic
        return self::$_ChallengerResponse;
    }

    protected static function _SendCurrent(Challenge $LatestChallenge): bool
    {
        // TODO: Implement current challenge logic
        return false;
    }

    protected static function _SendRandom(Challenge $LatestChallenge): ?ChallengerResponse
    {
        // TODO: Implement random challenge logic
        return null;
    }

    protected static function _SendEOL(): ?ChallengerResponse
    {
        // TODO: Implement end of lesson challenge logic
        return self::$_ChallengerResponse;
    }
}
