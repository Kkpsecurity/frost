<?php
declare(strict_types=1);

namespace App\Console\Commands;

use App;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

use App\Helpers\DateHelpers;
use App\Models\CourseDate;


class ExtendClassHours extends Command
{

    protected $signature   = 'command:extend_class_hours';
    protected $description = 'Extend Class Hours for Devel';


    public function handle() : int
    {

        if ( App::environment( 'production' ) )
        {
            $this->error( 'Refusing to run in production' );
            return 1;
        }


        $CourseDates = CourseDate::where( 'starts_at', '>', DateHelpers::DayStartSQL() )
                               ->orderBy( 'starts_at' )
                               ->get();


        $updated = 0;
        foreach ( $CourseDates as $CourseDate )
        {

            $CourseDate->ends_at = Carbon::parse( $CourseDate->ends_at )
                                            ->tz( 'America/New_York' )
                                          ->hour( 23 )->minute( 59 )->second( 59 )
                                            ->tz( 'UTC' );

            $CourseDate->save();

            $updated++;

        }
        $this->line( "{$updated} records updated." );

        return 0;

    }

}
