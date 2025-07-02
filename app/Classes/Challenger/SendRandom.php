<?php
declare(strict_types=1);

namespace App\Classes\Challenger;

use Illuminate\Support\Carbon;

use App\Classes\ChallengerResponse;
use App\Models\Challenge;


trait SendRandom
{

    protected static function _SendRandom( Challenge $Challenge ) : ?ChallengerResponse
    {

        $debug_tag = "_SendRandom(CH:{$Challenge->id})";


        //
        // Instructor Lesson is paused
        //

        if ( self::$_StudentLesson->InstLesson->is_paused )
        {
            kkpdebug( 'Challenger_Dbg', "{$debug_tag} InstLesson->is_paused" );
            return null;
        }


        $CreatedAt = Carbon::parse( $Challenge->created_at );

        //
        // too soon
        //

        if ( Carbon::now()->lt( ( clone $CreatedAt )->addSeconds( self::$_config->lesson_random_min ) ) )
        {
            kkpdebug( 'Challenger_Dbg', "{$debug_tag} [too soon]" );
            return null;
        }

        //
        // end of window; send now
        //

        if ( Carbon::now()->gt( ( clone $CreatedAt )->addSeconds( self::$_config->lesson_random_max ) ) )
        {
            kkpdebug( 'Challenger_Msg', "{$debug_tag} Creating Challenge [end of window]" );
            return self::_CreateChallenge();
        }

        //
        // random selection
        //

        if ( self::_Randomizer( clone $CreatedAt, self::$_config->lesson_random_max ) )
        {
            kkpdebug( 'Challenger_Msg', "{$debug_tag} Creating Challenge" );
            return self::_CreateChallenge();
        }

        kkpdebug( 'Challenger_Dbg', "{$debug_tag} [not random]" );
        return null;

    }

}
