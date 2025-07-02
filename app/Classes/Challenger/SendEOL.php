<?php
declare(strict_types=1);

namespace App\Classes\Challenger;

use Exception;
use Illuminate\Support\Carbon;

use App\Classes\ChallengerResponse;
use App\Models\Challenge;
#use App\Models\StudentLesson;


trait SendEOL
{

    protected static function _SendEOL() : ?ChallengerResponse
    {

        $debug_tag = '_SendEOL(SL:' . self::$_StudentLesson->id . ')';


        if ( self::$_StudentLesson->completed_at )
        {
            kkpdebug( 'Challenger_eol', "{$debug_tag} StudentLesson->completed_at" );
            return null;
        }

        if ( self::$_StudentLesson->dnc_at )
        {
            kkpdebug( 'Challenger_eol', "{$debug_tag} StudentLesson->dnc_at" );
            return null;
        }

        if ( ! self::$_StudentLesson->InstLesson->completed_at )
        {
            kkpdebug( 'Challenger_eol', "{$debug_tag} ! InstLesson->completed_at" );
            return null;
        }



        //
        // insufficient completed Challenges ?
        //

        /* TODO Revisit this
        $Challenges = self::$_StudentLesson->Challenges->sortBy( 'created_at' );

        if ( $Challenges->whereNotNull( 'completed_at' )->count() < self::_MinChallenges() )
        {

            kkpdebug( 'Challenger_Msg', "{$debug_tag} Insufficient Challenges *** Marking StudentLesson DNC ***' );

            self::$_StudentLesson->MarkDNC();

            return null;

        }
        */


        //
        // inspect last Challenge
        //

        if ( $LatestChallenge = self::$_StudentLesson->LatestChallenge )
        {

            //
            // ignore already marked Challenge
            //

            if ( ! $LatestChallenge->completed_at && ! $LatestChallenge->failed_at )
            {

                //
                // did not complete final Challenge in time
                //

                if ( $LatestChallenge->is_final && Carbon::now()->gt( Carbon::parse( $LatestChallenge->expires_at ) ) )
                {

                    kkpdebug( 'Challenger_Msg', "{$debug_tag} Missed FINAL window *** Marking Failed ***" );
                    self::MarkFailed( $LatestChallenge );

                    return null;

                }

                //
                // validate current EOL Challenge
                //

                if ( $LatestChallenge->is_eol )
                {

                    if ( Carbon::now()->gt( Carbon::parse( $LatestChallenge->expires_at ) ) )
                    {

                        kkpdebug( 'Challenger_Msg', "{$debug_tag} Missed EOL window *** Marking Failed ***" );
                        self::MarkFailed( $LatestChallenge );

                        return null;

                    }

                    kkpdebug( 'Challenger_Msg', "{$debug_tag} Sending Current EOL (CH:{$LatestChallenge->id})" );
                    return self::$_ChallengerResponse->SetChallenge( $LatestChallenge );

                }


                //
                // reuse last Challenge
                //

                kkpdebug( 'Challenger_Msg', "{$debug_tag} Reusing last Challenge (CH:{$LatestChallenge->id})" );
                return self::_SetChallengeEOL( $LatestChallenge );

            }

        }


        //
        // ready
        //

        kkpdebug( 'Challenger_Msg', "{$debug_tag} Creating EOL Challenge" );
        return self::_CreateChallenge([ 'is_eol' => true ]);

    }


    protected static function _MinChallenges() : int
    {

        $lesson_minutes = self::$_StudentLesson->GetCourseUnitLesson()->progress_minutes;

        //
        // short lesson; ie 30min
        //

        if ( $lesson_minutes <= ( ( self::$_config->lesson_start_max + self::$_config->lesson_random_max ) / 60 ) )
        {
            return 1;
        }

        //
        // ( hours * 2 ) - 1
        //   minimum 2
        //

        $min = intval( ( $lesson_minutes / 60 ) * 2 ) - 1;

        return $min < 2 ? 2 : $min;

    }


}
