<?php


function sat_new_course_dates()
{

    #$is_monday = date( 'w' ) <= 1;
    $is_monday = true;


    //
    // clean all weeks ahead of now
    //

    $delete_start_date = Illuminate\Support\Carbon::now( 'UTC' )->startOfWeek();

    if ( ! $is_monday ) $delete_start_date->addWeeks( 1 );

    $count = App\Models\CourseDate::where( 'starts_at', '>', $delete_start_date )->delete();

    $html  = "Deleted {$count} CourseDates starting "
           . $delete_start_date->isoFormat( 'ddd MM/DD HH:mm:ssZ' )
           . "<br /><br />\n";

    //
    // reset sequence
    //

    \DB::query( \DB::raw( "SELECT sp_resetseq( 'course_dates' )" ) );



    //
    // create records starting next week
    //


    // if today > Monday, advance to next week
    $weeknum_start = ( $is_monday ? 0 : 1 );

    foreach ( range( $weeknum_start, $weeknum_start + 3 ) as $week )
    {

        $html .= "<b>Week: {$week}</b><br />\n";

        $day = Illuminate\Support\Carbon::now( 'UTC' )->startOfWeek()->addWeeks( $week );


        foreach ( range( 1, 5 ) as $course_unit_id )
        {

            $starts_at = Illuminate\Support\Carbon::parse( $day->isoFormat( 'YYYY-MM-DD 09:00:00' ), 'America/New_York' );
            $ends_at   = Illuminate\Support\Carbon::parse( $day->isoFormat( 'YYYY-MM-DD 23:59:59' ), 'America/New_York' );


            $html .= $course_unit_id
                   . ' - ' . $starts_at->isoFormat( 'YYYY-MM-DD HH:mm:ssZ' )
                   . ' - ' . $ends_at->isoFormat( 'YYYY-MM-DD HH:mm:ssZ' )
                   . "<br />\n";


            App\Models\CourseDate::create([
                'course_unit_id' => $course_unit_id,
                'starts_at'      => $starts_at,
                'ends_at'        => $ends_at,
            ]);


            $day->addDays( 1 );

        }

    }

    return $html;

}


function sat_extend_hours()
{

    $res = '';

    foreach ( App\Models\CourseDate::all() as $CourseDate )
    {

        $old_ends_at = $CourseDate->EndsAt();

        $new_ends_at = Illuminate\Support\Carbon::parse( $CourseDate->ends_at, 'America/New_York' )
                     ->setHour( 23 )
                     ->setMinute( 59 )
                     ->setSecond( 59 )
                     ->isoFormat( 'YYYY-MM-DD HH:mm:ss' );

        $CourseDate->update([
            'ends_at' => $new_ends_at . '-04'
        ]);

        $res .= "{$old_ends_at} :: {$CourseDate->EndsAt()}<br />\n";

    }

    return $res;

}
