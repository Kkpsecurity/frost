<?php
declare(strict_types=1);

namespace App\Console\Commands;

use App;
use DB;
use Illuminate\Console\Command;

use App\Classes\ResetRecords;


class ResetClassroom extends Command
{

    protected $signature   = 'command:reset_classroom';
    protected $description = 'Reset Classroom Data';


    public function handle() : int
    {

        if ( App::environment( 'production' ) )
        {
            $this->error( 'Refusing to run in production' );
            return 1;
        }

        print ResetRecords::ResetClassroom( true );

        return 0;

    }

}
