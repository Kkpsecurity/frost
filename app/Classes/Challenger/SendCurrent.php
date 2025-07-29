<?php

declare(strict_types=1);

namespace App\Classes\Challenger;

use App\Models\Challenge;


trait SendCurrent
{
    protected static $_ChallengerResponse;

    /**
     * Sends the current challenge.
     *
     * @param Challenge $Challenge
     * @return bool
     */

    protected static function _SendCurrent(Challenge $Challenge): bool
    {

        #$debug_tag = "_SendCurrent({$Challenge->id})";

        if (self::_ValidateChallenge($Challenge)) {

            #kkpdebug( 'Challenger_Dbg', "{$debug_tag} TRUE" );
            self::$_ChallengerResponse->SetChallenge($Challenge);
            return true;
        }

        #kkpdebug( 'Challenger_Dbg', "{$debug_tag} FALSE" );
        return false;
    }
}
