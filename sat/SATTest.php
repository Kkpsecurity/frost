<?php
declare(strict_types=1);


#use Auth;
#use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;


use App\Classes\Payments\PayPalRESTObj;
use App\Http\Controllers\Web\EnrollmentController;
use App\Http\Controllers\Web\Payments\PayFlowProController;


use App\RCache;
use App\Classes\Challenger;
use App\Classes\ChatLogCache;
use App\Classes\ClassroomQueries;
use App\Classes\CourseAuthObj;
use App\Classes\ExamAuthObj;
use App\Classes\Keymaster;
use App\Classes\MiscQueries;
use App\Classes\PaymentQueries;
use App\Classes\ResetRecords;
use App\Classes\TrackingQueries;
use App\Classes\VideoCallRequest;
use App\Helpers\DateHelpers;
use App\Helpers\DevHelpers;
use App\Helpers\RangeSelect;
use App\Helpers\Helpers;
use App\Models\Course;
use App\Models\CourseAuth;
use App\Models\CourseDate;
use App\Models\CourseUnit;
use App\Models\Challenge;
use App\Models\ChatLog;
use App\Models\DiscountCode;
use App\Models\ExamAuth;
use App\Models\ExamQuestion;
use App\Models\InstLesson;
use App\Models\InstLicense;
use App\Models\InstUnit;
use App\Models\Order;
use App\Models\Payments\PayFlowPro;
use App\Models\Range;
use App\Models\RangeDate;
use App\Models\SelfStudyLesson;
use App\Models\SiteConfig;
use App\Models\StudentLesson;
use App\Models\StudentUnit;
use App\Models\User;
use App\Models\UserPref;
use App\Models\Validation;
use App\Models\ZoomCreds;
use KKP\Laravel\JSData;
use KKP\Laravel\PgTk;



class SATTest
{

    use App\Http\Controllers\React\Traits\SelfStudyTrait;



    public function TestMe()
    {


    }


    public function Display()
    {

        #
        # DNR
        #
        #return $this->NewCourseDates();
        #return $this->SelfStudyLesons();
        #return $this->KKPS3Test();
        #
        # return SAT_Header() . $html . SAT_Footer();
        #
        # $CourseDate = DevHelpers::CurrentCourseDate();
        # $RandomUser = User::inRandomOrder()->first();
        #
        #include base_path( '/sat/chat_logs.php' );
        #include base_path( '/sat/videocallrequest.php' );
        #include base_path( '/sat/challenger.php' );
        #include base_path( '/sat/igbdemo.php' );

        if ( $dumpdata ?? false ) { return dumpcap( $dumpdata ); }



        /*
        $html = '<table border="0" cellspacing="0" cellpadding="0">' . "\n";

        foreach ( RCache::DiscountCodes()->whereNotNull( 'client' )->sortBy( 'client' ) as $DiscountCode )
        {

            $view_route = route( 'discount_codes.usage',     $DiscountCode );
            $csv_route  = route( 'discount_codes.usage.csv', $DiscountCode );

            $html .=<<<ROW
<tr>
  <td><a href="{$view_route}" target="_blank">View</a></td>
  <td><a href="{$csv_route}">CSV</a></td>
  <td>{$DiscountCode->max_count}</td>
  <td>{$DiscountCode->code}</td>
  <td>{$DiscountCode->client}</td>
</tr>

ROW;
        }

        return SAT_Header() . $html . "</table>\n" . SAT_Footer();
        */


        /*
        return dumpcap(
            SiteConfig::create([
                'cast_to'       => 'int',
                'config_name'   => 'instructor_post_end_minutes',
                'config_value'  => '30',
            ])
        );
        */

        #
        # default
        #

        include base_path( '/sat/dump_courses.php' );
        return sat_dump_courses();

    }


    public function NewCourseDates()
    {
        include base_path( '/sat/new_course_dates.php' );
        return sat_new_course_dates();
    }

    public function ExtendCourseHours()
    {
        include base_path( '/sat/new_course_dates.php' );
        return sat_extend_hours();
    }

    public function SelfStudyLesons()
    {
        include base_path( '/sat/self_study_lessons.php' );
        return self_study_lessons();
    }

    public function KKPS3Test()
    {
        include base_path( '/sat/kkps3.php' );
        return kkps3_test();
    }



    //
    // helpers
    //


    public function ISODate( $timestamp ) : string
    {

        if ( ! $timestamp )
        {
            return '';
        }

        if ( ! is_a( $timestamp, 'Illuminate\Support\Carbon' ) )
        {
            $timestamp = Carbon::parse( $timestamp );
        }

        return $timestamp->tz( 'America/New_York' )->isoFormat( 'ddd MM/DD HH:mm:ss.SSSSSS zz' );

    }



}
