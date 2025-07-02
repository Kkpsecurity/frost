<?php
declare(strict_types=1);

namespace App\Classes;

use Auth;
use DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

use RCache;
use App\Helpers\DateHelpers;
use App\Models\CourseDate;
use App\Models\InstUnit;
use App\Models\InstLesson;
use App\Models\StudentUnit;
use App\Models\StudentLesson;
use KKP\Laravel\PgTk;


class TrackingQueries
{


    /* moved to ClassroomQueries */
    public static function InstructorDashboardCourseDates()
    {

        return CourseDate::where( 'starts_at', '>=', DateHelpers::DayStartSQL() )
                         ->where( 'ends_at',   '<=', DateHelpers::DayEndSQL() )
                         ->where( 'is_active', true )
                          ->with( 'InstUnit' )
                           ->get()
                        ->filter(function( $CourseDate ) {

            return Carbon::now()->addMinutes(
                        RCache::SiteConfig( 'instructor_pre_start_minutes' )
                    )->gt( Carbon::parse( $CourseDate->starts_at ) )
                    &&
                    Carbon::parse( $CourseDate->ends_at )->addMinutes(
                        RCache::SiteConfig( 'instructor_post_end_minutes' )
                    )->gt( Carbon::now() );

        });

    }


    //
    //
    //


    /* moved to ClassroomQueries */
    public static function ActiveInstLesson( ?InstUnit $InstUnit ) : ?InstLesson
    {

        if ( ! $InstUnit )
        {
            kkpdebug( 'TrackingQueries_Dbg', 'ActiveInstLesson :: No InstUnit' );
            return null;
        }

        return InstLesson::where( 'inst_unit_id', $InstUnit->id )
                     ->whereNull( 'completed_at' )
                        ->latest()
                         ->first();

    }


    /* moved to ClassroomQueries */
    public static function CompletedInstLessons( ?InstUnit $InstUnit ) : ?Collection
    {

        if ( ! $InstUnit )
        {
            kkpdebug( 'TrackingQueries_Dbg', 'CompletedInstLessons :: No InstUnit' );
            return null;
        }

        return InstLesson::where( 'inst_unit_id', $InstUnit->id )
                  ->whereNotNull( 'completed_at' )
                       ->orderBy( 'completed_at' )
                           ->get();

    }


    //
    //
    //


    /* moved to ClassroomQueries::InitStudentLesson */
    public static function ActiveStudentUnits( CourseDate $CourseDate ) : Collection
    {

        return StudentUnit::where( 'course_date_id', $CourseDate->id )->get();

    }


    /* moved to ClassroomQueries */
    public static function ActiveStudentLesson( ?StudentUnit $StudentUnit ) : ?StudentLesson
    {

        if ( ! $StudentUnit )
        {
            kkpdebug( 'TrackingQueries_Dbg', 'ActiveStudentLesson :: No StudentUnit' );
            return null;
        }


        kkpdebug( 'TrackingQueries_Dbg', "ActiveStudentLesson :: StudentUnitID {$StudentUnit->id} - InstUnitID {$StudentUnit->InstUnit->id}" );


        if ( ! $InstLesson = self::ActiveInstLesson( $StudentUnit->InstUnit ) )
        {
            kkpdebug( 'TrackingQueries_Dbg', 'ActiveStudentLesson :: No InstLesson' );
            return null;
        }


        if ( $StudentLesson = StudentLesson::where( 'student_unit_id', $StudentUnit->id )
                                           ->where( 'inst_lesson_id',  $InstLesson->id  )
                                           ->first()
        )
        {
            kkpdebug( 'TrackingQueries_Dbg', "ActiveStudentLesson :: Returning StudentLessonID {$StudentLesson->id}" );
            return $StudentLesson;
        }


        if ( ! self::_CanJoinLesson( $StudentUnit, $InstLesson ) )
        {
            kkpdebug( 'TrackingQueries_Msg', 'ActiveStudentLesson TOO LATE' );
            return null;
        }


        //
        //
        //

        $locker_key = "ActiveStudentLesson:{$StudentUnit->id}:{$InstLesson->lesson_id}";

        if ( ! RCache::Locker( $locker_key, 5 ) )
        {
        	logger( "TrackingQueries::ActiveStudentLesson failed to get lock -- {$locker_key}" );
        	return null;
        }


        kkpdebug( 'TrackingQueries_Msg', "Creating StudentLesson student_unit_id {$StudentUnit->id} lesson_id {$InstLesson->lesson_id} inst_lesson_id {$InstLesson->id}" );

        $StudentLesson = StudentLesson::create([

            'student_unit_id'   => $StudentUnit->id,
            'lesson_id'         => $InstLesson->lesson_id,
            'inst_lesson_id'    => $InstLesson->id,

        ])->refresh();

        RCache::Locker( $locker_key, 0 );

        return $StudentLesson;

    }


    /* moved to ClassroomQueries */
    protected static function _CanJoinLesson( StudentUnit $StudentUnit, InstLesson $InstLesson ) : bool
    {

        if ( $StudentUnit->GetUser()->IsAnyAdmin() )
        {
            return true;
        }

        return Carbon::now()->lt(
                    Carbon::parse( $InstLesson->created_at )->addSeconds( RCache::SiteConfig( 'student_join_lesson_seconds' ) )
               );

    }


    /* moved to ClassroomQueries */
    public static function EOLStudentLesson( int $student_unit_id ) : ?StudentLesson
    {

        return StudentLesson::where( 'student_unit_id', $student_unit_id )
                        ->whereNull( 'dnc_at' )
                        ->whereNull( 'completed_at' )
                              ->get()->last();

    }



    //
    // data writers
    //


    /*
    public static function CompleteStudentLesson( StudentLesson $StudentLesson, bool $inst_set_complete = false ) : bool
    {

        //
        // instructor override
        //

        if ( $inst_set_complete )
        {

            $StudentLesson->update([
                'dnc_at'        => null,
                'completed_at'  => PgTk::now(),
                'completed_by'  => Auth::id(),
            ]);

            return true;

        }

        //
        // verify StudentLesson can be completed
        //

        if ( $StudentLesson->dnc_at )
        {
            return false;
        }

        if ( $StudentLesson->StudentUnit->ejected_at )
        {
            return false;
        }


        // TODO: banned student


        $StudentLesson->pgtouch( 'completed_at' );

        return true;

    }
    */


    /* moved to ClassroomQueries */
    public static function InitInstUnit( CourseDate $CourseDate ) : ?InstUnit
    {

        $locker_key = "InitInstUnit:{$CourseDate->id}";

        if ( ! RCache::Locker( $locker_key, 5 ) )
        {
        	logger( "TrackingQueries::InitInstUnit failed to get lock -- {$locker_key}" );
        	return null;
        }

        $InstUnit = InstUnit::create([

            'course_date_id' => $CourseDate->id,
            'created_by'     => Auth::id(),

        ])->refresh();

        RCache::Locker( $locker_key, 0 );

        return $InstUnit;

    }


    /* moved to ClassroomQueries */
    public static function InitInstLesson( InstUnit $InstUnit, int $lesson_id ) : ?InstLesson
    {


        if ( $InstLesson = InstLesson::where( 'inst_unit_id', $InstUnit->id )
                                     ->where( 'lesson_id', $lesson_id )
                                     ->first()
        )
        {
            logger( 'InstLesson already created' );
            return $InstLesson;
        }


        $locker_key = "InitInstLesson:{$InstUnit->id}:{$lesson_id}";

        if ( ! RCache::Locker( $locker_key, 5 ) )
        {
        	logger( "TrackingQueries::InitInstLesson failed to get lock -- {$locker_key}" );
        	return null;
        }

        $InstLesson = InstLesson::create([

            'inst_unit_id' => $InstUnit->id,
            'lesson_id'    => $lesson_id,
            'created_by'   => Auth::id(),

        ])->refresh();

        RCache::Locker( $locker_key, 0 );

        return $InstLesson;

    }


    /* moved to ClassroomQueries */
    public static function CompleteInstUnit( InstUnit $InstUnit ) : void
    {

        if ( $InstUnit->completed_at )
        {
            logger( "TrackingQueries::CompleteInstUnit({$InstUnit->id}) :: InstUnit already completed" );
            return;
        }


        $InstUnit->update([
            'completed_at' => PgTk::now(),
            'completed_by' => ( Auth::id() ?? $InstUnit->created_by )
        ]);


        foreach ( $InstUnit->StudentUnits as $StudentUnit )
        {
            if ( ! $StudentUnit->completed_at && ! $StudentUnit->dnc_at )
            {
                $StudentUnit->pgtouch( 'completed_at' );
            }
        }

    }


    /* moved to ClassroomQueries */
    public static function RecentInstUnits( int $user_id, int $days = 30 ) : Collection
    {

        //
        // stored procedure is *much* faster
        //

        return PgTk::toModels(
                    InstUnit::class,
                    DB::select( 'SELECT * FROM sp_recent_instunits( :user_id, :days )', [
                        'user_id' => $user_id,
                        'days'    => $days,
                    ])
               );

    }

}
