<?php
declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use RCache;
#use App\Models\CourseAuth;
#use App\Models\CourseDate;
#use App\Models\User;
#use App\Models\UserPref;
use KKP\Laravel\PgTk;


class Today extends Command
{

    protected $signature   = 'command:today';
    protected $description = 'Todays Updates';


    public function handle() : int
    {

        // DNR
        return 0;

    }


}
