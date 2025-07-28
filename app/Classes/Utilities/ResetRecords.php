<?php

declare(strict_types=1);

namespace App\Classes\Utilities;

/**
 * @file ResetRecords.php
 * @brief Class for resetting classroom records and creating class dates.
 * @details Provides methods to reset classroom tables and create course dates.
 */


use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;

use App\Services\RCache;

use App\Models\ChatLog;
use App\Models\InstUnit;
use App\Models\Validation;
use App\Models\CourseDate;
use App\Models\StudentUnit;

use App\Classes\Frost\ChatLogCache;


class ResetRecords
{


    public static $iso_fmt = 'ddd MM/DD HH:mm';


    public static function ResetClassroom($is_command = false): string
    {

        $cmd_res  = '';
        $html_res = '<h3>' . __METHOD__ . "</h3>\n";

        $tables = [
            'chat_logs',
            'challenges',
            'validations',
            'student_lesson',
            'student_unit',
            'inst_lesson',
            'inst_unit'
        ];

        foreach ($tables as $table) {

            $msg = "Truncating table {$table}";

            $cmd_res  .= "{$msg}\n";
            $html_res .= "{$msg}<br />\n";

            DB::select(DB::raw("TRUNCATE {$table} RESTART IDENTITY CASCADE"));
        }


        $cmd_res  .= "\n";
        $html_res .= "<br />\n";


        foreach (['cache', 'rcache'] as $dbname) {

            $msg = "Flushing redis DB '{$dbname}'";

            $cmd_res  .= "{$msg}\n";
            $html_res .= "{$msg}<br />\n";

            Redis::Connection($dbname)->flushdb();
        }


        $msg = 'Resetting all CourseAuth->agreed_at';
        $cmd_res  .= "{$msg}\n";
        $html_res .= "{$msg}<br />\n";
        DB::select(DB::raw('UPDATE course_auths SET agreed_at = NULL'));

        $msg = 'Resetting all ZoomCreds->zoom_status';
        $cmd_res  .= "{$msg}\n";
        $html_res .= "{$msg}<br />\n";
        DB::select(DB::raw("UPDATE zoom_creds SET zoom_status = 'disabled'"));

        return ($is_command ? $cmd_res : $html_res);
    }


    /*
    public static function MakeClassDates( $is_command = false ) : string
    {
        return 'MakeClassDates is now disabled';
    }
    */


    public static function MakeClassDates($is_command = false): string
    {

        $create_weeks = 12;

        $begin_date = Carbon::now()->startOfWeek();


        //
        // reset Classroom tables first
        //

        if ($is_command) {
            $cmd_res  = self::ResetClassroom(true) . "\n";
            $html_res = '<h3>' . __METHOD__ . "</h3>\n";
        } else {
            $cmd_res  = '';
            $html_res = self::ResetClassroom(false) . "<br />\n"
                . '<h3>' . __METHOD__ . "</h3>\n";
        }


        //
        // clear table
        //

        DB::select(DB::raw('TRUNCATE course_dates RESTART IDENTITY CASCADE'));

        $cmd_res  .= "TRUNCATE course_dates\n\n";
        $html_res .= "TRUNCATE course_dates<br /><br />\n";


        //
        //
        //

        $Course = RCache::Courses(1);


        foreach (range(0, $create_weeks - 1) as $week) {

            $cmd_res  .= "Creating week {$week}\n";
            $html_res .= "<b>Week: {$week}</b><br />\n";


            // first day of this week
            $first_of_week = (clone $begin_date)->addWeeks($week);


            foreach ($Course->dates_template['week_1'] as $template_day) {

                list($start_hour, $start_min) = explode(':', $template_day['start']);
                list($end_hour,   $end_min) = explode(':', $template_day['end']);

                $starts_at = (clone $first_of_week)->timezone('America/New_York')->addDays($template_day['wday'])->hour($start_hour)->minute($start_min);
                $ends_at   = (clone $first_of_week)->timezone('America/New_York')->addDays($template_day['wday'])->hour($end_hour)->minute($end_min);


                $html_res .= RCache::CourseUnits($template_day['course_unit_id'])->admin_title
                    . ' - ' . $starts_at->isoFormat(self::$iso_fmt)
                    . ' - ' . $ends_at->isoFormat(self::$iso_fmt)
                    . "<br />\n";


                CourseDate::create([
                    'course_unit_id' => $template_day['course_unit_id'],
                    'starts_at'      => $starts_at->tz('UTC'),
                    'ends_at'        => $ends_at->tz('UTC'),
                ]);
            } // end template_day


            $html_res .= "<br />\n";
        } // end week


        return ($is_command ? $cmd_res : $html_res);
    }



    /*
    public static function MakeClassDates( $is_command = false ) : string
    {

        $create_weeks = 4;

        //
        // reset Classroom tables first
        //

        if ( $is_command )
        {
            $cmd_res  = self::ResetClassroom( true ) . "\n";
            $html_res = '<h3>' . __METHOD__ . "</h3>\n";
        }
        else
        {
            $cmd_res  = '';
            $html_res = self::ResetClassroom( $is_command ) . "<br />\n"
                      . '<h3>' . __METHOD__ . "</h3>\n";
        }



        //
        // clean all weeks starting this week
        //

        $delete_start_date = Carbon::now( 'UTC' )->startOfWeek();

        $count = CourseDate::where( 'starts_at', '>', $delete_start_date )->delete();

        $msg = "Deleted {$count} CourseDates starting " . $delete_start_date->isoFormat( self::$iso_fmt );

        $cmd_res  .= "{$msg}\n";
        $html_res .= "{$msg}<br /><br />\n";



        //
        // reset sequence
        //

        DB::select( DB::raw( "SELECT sp_resetseq( 'course_dates' )" ) );



        //
        // create records
        //

        foreach ( range( 0, $create_weeks-1 ) as $week )
        {

            $cmd_res  .= "Creating week {$week}\n";
            $html_res .= "<b>Week: {$week}</b><br />\n";


            // first day of week
            $day = Carbon::now( 'UTC' )->startOfWeek()->addWeeks( $week );

            foreach ( range( 1, 5 ) as $course_unit_id )
            {

                #$starts_at = Carbon::parse( $day )->hour(  9 )->minute(  0 )->second(  0 )->timezone( 'UTC' );
                #$ends_at   = Carbon::parse( $day )->hour( 23 )->minute( 59 )->second( 59 )->timezone( 'UTC' );

                $starts_at = Carbon::parse( $day )->hour( 13 )->minute( 0 )->second( 0 )->timezone( 'UTC' );
                $ends_at   = Carbon::parse( $day )->hour( 21 )->minute( 0 )->second( 0 )->timezone( 'UTC' );

                $html_res .= $course_unit_id
                           . ' - ' . $starts_at->isoFormat( self::$iso_fmt )
                           . ' - ' . $ends_at->isoFormat( self::$iso_fmt )
                           . "<br />\n";


                CourseDate::create([
                    'course_unit_id' => $course_unit_id,
                    'starts_at'      => $starts_at,
                    'ends_at'        => $ends_at,
                ]);


                $day->addDays( 1 );

            }

        }



        #$CourseDate = CourseDate::first();
        #return Carbon::parse( $CourseDate->starts_at )->timezone( 'America/New_York' ) . "\n"
        #     . Carbon::parse( $CourseDate->ends_at   )->timezone( 'America/New_York' ) . "\n";


        return ( $is_command ? $cmd_res : $html_res );

    }
    */


    //
    //
    //


    public static function ResetCourseDate(CourseDate $CourseDate): string
    {


        if (! $InstUnit = InstUnit::firstWhere('course_date_id', $CourseDate->id)) {
            return 'No InstUnit';
        }

        $chat_logs       = 0;
        $inst_lessons    = 0;
        $student_units   = 0;
        $student_lessons = 0;
        $challenges      = 0;
        $validations     = 0;


        //
        // ChatLogs
        //


        foreach (ChatLog::where('course_date_id', $CourseDate->id)->get() as $ChatLog) {
            $chat_logs++;
            $ChatLog->delete();
        }

        ChatLogCache::Disable($CourseDate->id);
        ChatLogCache::Devel_Purge($CourseDate->id);



        //
        // StudentUnits / StudentLessons / Challenges
        //


        foreach (StudentUnit::where('inst_unit_id', $InstUnit->id)->get() as $StudentUnit) {

            $student_units++;

            foreach ($StudentUnit->StudentLessons as $StudentLesson) {

                $student_lessons++;

                foreach ($StudentLesson->Challenges as $Challenge) {
                    $challenges++;
                    $Challenge->delete();
                }

                $StudentLesson->delete();
            }

            if ($Validation = $StudentUnit->validation) {
                $validations++;
                $Validation->delete();
            }

            $StudentUnit->delete();
        }


        //
        // InstLessons
        //


        foreach ($InstUnit->InstLessons as $InstLesson) {

            $inst_lessons++;
            $InstLesson->delete();
        }


        //
        // InstUnit
        //

        $InstUnit->delete();


        //
        // caches
        //

        $RedisConn = Cache::store('redis')->connection();
        foreach ($RedisConn->keys('previous_completed_lessons:*') as $key) {
            $RedisConn->del($key);
        }


        DB::select(DB::raw('UPDATE course_auths SET agreed_at = NULL'));
        DB::select(DB::raw("UPDATE zoom_creds SET zoom_status = 'disabled'"));


        return "Deleted {$inst_lessons} Instructor Lessons<br />\n"
            . "Deleted {$student_units} Student Units<br />\n"
            . "Deleted {$student_lessons} Student Lessons<br />\n"
            . "Deleted {$challenges} Challenges<br />\n"
            . "Deleted {$chat_logs} ChatLogs<br />\n"
            . "Deleted {$validations} Validations<br />\n"
            . "Deleted 1 Instructor Unit<br />\n";
    }
}
