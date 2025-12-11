<?php
declare(strict_types=1);

namespace App\Classes\Challenger;

use App\Classes\Challenger\CreateChallenge;
use App\Classes\Challenger\CreateClearedDNC;
use App\Classes\Challenger\SendCurrent;
use App\Classes\Challenger\SendEOL;
use App\Classes\Challenger\SendFirst;
use App\Classes\Challenger\SendFinal;
use App\Classes\Challenger\SendRandom;


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
