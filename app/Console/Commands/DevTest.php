<?php
declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

use RCache;
use App\Helpers\DateHelpers;
use App\Models\CourseDate;


class DevTest extends Command
{

    protected $signature   = 'command:devtest';
    protected $description = 'Devel Test';


    public function handle() : int
    {

        $CourseDates = CourseDate::where( 'starts_at', '>=', DateHelpers::DayStartSQL() )
                                 ->where( 'ends_at',   '<=', DateHelpers::DayEndSQL() )
                               ->orderBy( 'course_unit_id' )
                                   ->get();

        $table_data = [];
        $new_start  = Carbon::now()->addMinutes( RCache::SiteConfig( 'instructor_pre_start_minutes' ) );

        foreach ( $CourseDates as $CourseDate )
        {

            $new_start->addMinutes( 1 )->seconds( 0 );

            $CourseDate->update([ 'starts_at' => $new_start ]);
            $CourseDate->refresh();

            $table_data[] = [

                $CourseDate->id,
                $CourseDate->GetCourse()->ShortTitle(),
                $CourseDate->GetCourseUnit()->title,
                $this->_FormatDate( $CourseDate->starts_at ),
                $this->_FormatDate( $CourseDate->ends_at   ),

            ];

        }


        $this->table(
            [ 'DateID', 'Course', 'Unit', 'Starts At', 'Ends At' ],
            $table_data
        );


        return 1;

    }


    private function _FormatDate( int|string $date ) : string
    {
        return Carbon::parse( $date )->tz( 'America/New_York' )->isoFormat( 'HH:mm:ss' );
    }


}
