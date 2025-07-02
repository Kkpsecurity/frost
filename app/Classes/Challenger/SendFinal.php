<?php
declare(strict_types=1);

namespace App\Classes\Challenger;

use Exception;
use Illuminate\Support\Carbon;

use App\Classes\ChallengerResponse;
use App\Models\Challenge;


trait SendFinal
{

    //
    // $Challenge = last failed Challenge
    //

    protected static function _SendFinal( Challenge $Challenge ) : ?ChallengerResponse
    {

        $debug_tag = "_SendFinal(CH:{$Challenge->id})";


        //
        // Instructor Lesson is paused
        //

        if ( self::$_StudentLesson->InstLesson->is_paused )
        {
            kkpdebug( 'Challenger_Dbg', "{$debug_tag} InstLesson->is_paused :: marking Completed" );
            $Challenge->MarkCompleted();
            return null;
        }


        $FailedAt  = Carbon::parse( $Challenge->failed_at );


        //
        // missed final challenge window ?
        //

        if ( Carbon::now()->gt( ( clone $FailedAt )->addSeconds( self::$_config->final_challenge_max ) ) )
        {

            kkpdebug( 'Challenger_Msg', "{$debug_tag} Missed FINAL window *** Marking StudentLesson DNC ***" );
            $Challenge->StudentLesson->MarkDNC();

            return null;

        }


        //
        // ready for final challenge ?
        //

        //
        // too soon
        //

        if ( Carbon::now()->lt( ( clone $FailedAt )->addSeconds( self::$_config->final_challenge_min ) ) )
        {
            kkpdebug( 'Challenger_Dbg', "{$debug_tag} [too soon]" );
            return null;
        }

        //
        // ready
        //

        kkpdebug( 'Challenger_Msg', "{$debug_tag} Creating FINAL Challenge" );
        return self::_CreateChallenge([ 'is_final' => true ]);

    }

}
