<?php
declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

use App\RCache;
#use App\Models\CourseAuth;
#use App\Models\DiscountCode;
#use App\Models\SiteConfig;
#use App\Models\User;



class TestCmd extends Command
{

    protected $signature   = 'command:test_cmd';
    protected $description = 'TestCmd';


    public function handle() : int
    {


        //
        // DNR
        //
        $this->info( 'TestCmd completed successfully' );
        return 0;

    }

}
