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
use App\Models\StudentLesson;
// use App\Classes\Challenger\TraitLoader; // Trait not found - commented out
use App\Classes\Frost\ChallengerResponse;
use App\Traits\AssertConfigTrait; // Fixed namespace


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


        $LatestChallenge = self::$_StudentLesson->LatestChallenge;


        //
        // beginning of Lesson
        //

        if (! $LatestChallenge) {
            return self::_SendFirst(); // ?ChallengerResponse
        }


        //
        // send Final challenge ?
        //

        if ($LatestChallenge->failed_at) {
            return self::_SendFinal($LatestChallenge); // ?ChallengerResponse
        }


        //
        // send Current Challenge ?
        //

        if (self::_SendCurrent($LatestChallenge)) // bool
        {
            kkpdebug('Challenger_Msg', "{$debug_tag} sending Current (CH:{$LatestChallenge->id})");
            return self::$_ChallengerResponse;
        }


        //
        // send random challenge ?
        //

        return self::_SendRandom($LatestChallenge); // ?ChallengerResponse

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
}
