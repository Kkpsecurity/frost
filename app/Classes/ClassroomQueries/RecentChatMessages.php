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


        if ( $user_id == InstUnit::firstWhere( 'course_date_id', $course_date_id )->created_by )
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

            return ChatLog::where( 'course_date_id', $course_date_id )
                      ->whereNull( 'hidden_at')
                          ->where( function ( $query ) use ( $user_id ) {
                                $query->where( 'student_id', $user_id )
                                      ->orWhereNotNull( 'inst_id' );
                        })
                        ->orderBy( 'id', 'desc' )
                          ->limit( $message_limit )
                            ->get()
                        ->reverse();

        }

    }

}
