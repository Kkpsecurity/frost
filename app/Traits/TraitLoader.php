<?php

declare(strict_types=1);

namespace App\Traits;

use App\Classes\Students\Challenger\CreateChallenge;
use App\Classes\Students\Challenger\CreateClearedDNC;
use App\Classes\Students\Challenger\SendCurrent;
use App\Classes\Students\Challenger\SendEOL;
use App\Classes\Students\Challenger\SendFirst;
use App\Classes\Students\Challenger\SendFinal;
use App\Classes\Students\Challenger\SendRandom;


trait TraitLoader
{

    use CreateChallenge;
    use CreateClearedDNC;
    use SendCurrent;
    use SendEOL;
    use SendFirst;
    use SendFinal;
    use SendRandom;
}
