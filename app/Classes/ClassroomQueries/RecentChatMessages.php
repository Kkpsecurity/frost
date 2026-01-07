<?php
declare(strict_types=1);

namespace App\Classes\ClassroomQueries;

use Illuminate\Support\Collection;

use App\Models\ChatLog;
use App\Models\CourseDate;
use App\Models\InstUnit;
use App\Models\User;


trait RecentChatMessages
{

    /**
     * Retrieve Recent ChatLog records
     *
     * @param   int|string|CourseDate  $CourseDate
     * @param   int|string|User        $User
     * @param   int                    $message_limit = 50
     * @return  Collection             [ChatLog]
     */
    public static function RecentChatMessages( int|string|CourseDate $CourseDate, int|string|User $User, int $message_limit = 50 ) : Collection
    {

        $course_date_id = $CourseDate->id ?? (int) $CourseDate;
        $user_id        = $User->id       ?? (int) $User;


  $instUnit = InstUnit::firstWhere( 'course_date_id', $course_date_id );
  $isInstructorForClass = $instUnit && ((int) $user_id === (int) $instUnit->created_by);

  if ( $isInstructorForClass )
        {

            return ChatLog::where( 'course_date_id', $course_date_id )
                      ->whereNull( 'hidden_at' )
                        ->orderBy( 'id', 'desc' )
                          ->limit( $message_limit )
                            ->get()
                        ->reverse();

        }
        else
        {

            // Student privacy rule:
            // Student only sees the 1:1 thread where student_id matches.
            // Instructor messages must be stored with the same student_id to appear.
            return ChatLog::where( 'course_date_id', $course_date_id )
                      ->whereNull( 'hidden_at')
                      ->where( 'student_id', $user_id )
                      ->orderBy( 'id', 'desc' )
                      ->limit( $message_limit )
                      ->get()
                      ->reverse();

        }

    }

}
