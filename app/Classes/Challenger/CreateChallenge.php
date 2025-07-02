<?php
declare(strict_types=1);

namespace App\Classes\Challenger;

use Illuminate\Support\Carbon;

use RCache;
use App\Classes\ChallengerResponse;
use App\Models\Challenge;


trait CreateChallenge
{


    protected static function _CreateChallenge( array $flags = null ) : ?ChallengerResponse
    {

        $debug_tag  = '_CreateChallenge(SL:' . self::$_StudentLesson->id . ')';

        $locker_key = 'CreateChallenge:' . self::$_StudentLesson->id;


        if ( ! RCache::Locker( $locker_key, 5 ) )
        {
        	kkpdebug( 'Challenger_ERR', "{$debug_tag} failed to get lock" );
        	return null;
        }


        $Challenge = Challenge::create([
                        'student_lesson_id' => self::$_StudentLesson->id,
                        'is_final'          => $flags['is_final'] ?? false,
                        'is_eol'            => $flags['is_eol']   ?? false,
                        'expires_at'        => Carbon::now()->addSeconds( self::$_config->challenge_expires_at ),
                     ]);


        RCache::Locker( $locker_key, 0 );


        kkpdebug( 'Challenger_Msg', "{$debug_tag} created"
                                    . ( ( $flags['is_final'] ?? false ) ? ' FINAL' : '' )
                                    . ( ( $flags['is_eol']   ?? false ) ? ' EOL'   : '' )
                                    . " CH:{$Challenge->id}"
                );


        return self::$_ChallengerResponse->SetChallenge( $Challenge );

    }


    protected static function _SetChallengeEOL( Challenge $Challenge ) : ChallengerResponse
    {

        //
        // don't update expires_at
        //

        if ( ! $Challenge->is_eol )
        {

            $Challenge->update([
                'is_eol'     => true,
                'expires_at' => Carbon::now()->addSeconds( self::$_config->challenge_expires_at ),
            ]);

            kkpdebug( 'Challenger_Msg', "_SetChallengeEOL() updated CH:{$Challenge->id}" );

        }
        else
        {
            kkpdebug( 'Challenger_Dbg', "_SetChallengeEOL() Challenge already is_eol" );
        }

        return self::$_ChallengerResponse->SetChallenge( $Challenge );

    }


}
