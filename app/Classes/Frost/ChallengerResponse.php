<?php

declare(strict_types=1);

namespace App\Classes\Frost;

/**
 * @file ChallengerResponse.php
 * @brief Class for handling responses to challenges.
 * @details This class encapsulates the response details for a challenge, including time, ID, and status flags.
 */

use App\Models\Challenge;


class ChallengerResponse
{

    public $challenge_time = 0;
    public $challenge_id   = null;
    public $is_final       = false;
    public $is_eol         = false;


    public function __construct(int $challenge_time)
    {
        $this->challenge_time = $challenge_time;
    }


    public function SetChallenge(Challenge $Challenge): self
    {

        $this->challenge_id = $Challenge->id;
        $this->is_final     = $Challenge->is_final;
        $this->is_eol       = $Challenge->is_eol;

        return $this;
    }
}
