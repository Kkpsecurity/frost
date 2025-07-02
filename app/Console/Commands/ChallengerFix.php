<?php
declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

use App\Models\Challenge;
use App\Models\InstLesson;
use App\Models\StudentUnit;



class ChallengerFix extends Command
{

    protected $signature   = 'command:challenger_fix';
    protected $description = 'Challenger Fix';


    public function handle() : int
    {

        $start_date = '2024-07-22 00:00:00-4';
        $end_date   = '2024-07-24 00:00:00-4';

        $student_units   = 0;
        $student_lessons = 0;

        $lessons_already = 0;
        $lessons_updated = 0;
        $lessons_skipped = 0;


        $InstLessons = InstLesson::where( 'created_at', '>=', $start_date )
                                 ->where( 'created_at', '<=', $end_date )
                                   ->get();

        $StudentUnits = StudentUnit::where( 'created_at', '>=', $start_date )
                                   ->where( 'created_at', '<=', $end_date )
                                   ->get();


        foreach ( $StudentUnits as $StudentUnit )
        {

            $student_units++;

            foreach ( $StudentUnit->StudentLessons as $StudentLesson )
            {

                $student_lessons++;


                //
                // StudentLesson already compeleted
                //

                if ( $StudentLesson->completed_at )
                {
                    $lessons_already++;
                    continue;
                }

                //
                // VerifyInstLesson is complete
                //

                $InstLesson = $InstLessons->firstWhere( 'id', $StudentLesson->inst_lesson_id );

                if ( $InstLesson->completed_at )
                {

                    $StudentLesson->update([
                        'dnc_at'       => null,
                        'completed_at' => Carbon::parse( $InstLesson->completed_at ),
                    ]);

                    $lessons_updated++;

                }
                else
                {

                    $lessons_skipped++;

                }

            }

        }


        $this->line( "{$InstLessons->count()} InstLessons" );
        $this->line( "{$student_units} StudentUnits" );
        $this->line( "{$student_lessons} StudentLessons" );
        $this->line( "{$lessons_already} StudentLessons Already Marked Completed" );
        $this->line( "{$lessons_updated} StudentLessons Updated" );
        $this->line( "{$lessons_skipped} StudentLessons Skipped" );


        # DNR
        return 0;

    }


}
