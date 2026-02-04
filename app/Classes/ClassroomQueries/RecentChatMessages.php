<?php

declare(strict_types=1);

namespace App\Classes\ClassroomQueries;

use Illuminate\Support\Collection;

use App\Models\ChatLog;
use App\Models\CourseDate;
use App\Models\InstUnit;
use App\Models\User;


/**
 * @deprecated This trait is DEPRECATED and contains outdated query logic.
 * @deprecated Use App\Classes\MiscQueries::RecentChatMessages() instead.
 * @deprecated This version does NOT include the hub-and-spoke fix from January 30, 2026.
 * @deprecated Students using this query will NOT see instructor replies.
 *
 * @see App\Classes\MiscQueries::RecentChatMessages() for the current implementation
 */
trait RecentChatMessages
{

    /**
     * Retrieve Recent ChatLog records
     *
     * @deprecated This method is DEPRECATED. Use App\Classes\MiscQueries::RecentChatMessages() instead.
     * @deprecated This implementation has outdated student query logic (missing instructor messages).
     *
     * @param   int|string|CourseDate  $CourseDate
     * @param   int|string|User        $User
     * @param   int                    $message_limit = 50
     * @return  Collection             [ChatLog]
     */
    public static function RecentChatMessages(int|string|CourseDate $CourseDate, int|string|User $User, int $message_limit = 50): Collection
    {

        $course_date_id = $CourseDate->id ?? (int) $CourseDate;
        $user_id        = $User->id       ?? (int) $User;


        $instUnit = InstUnit::firstWhere('course_date_id', $course_date_id);
        $isInstructorForClass = $instUnit && ((int) $user_id === (int) $instUnit->created_by);

        if ($isInstructorForClass) {

            return ChatLog::where('course_date_id', $course_date_id)
                ->whereNull('hidden_at')
                ->orderBy('id', 'desc')
                ->limit($message_limit)
                ->get()
                ->reverse();
        } else {

            // @deprecated OUTDATED LOGIC: This query is missing instructor messages!
            // @deprecated Student privacy rule should be:
            // @deprecated - Students see their own messages (student_id matches, inst_id is null)
            // @deprecated - Students see ALL instructor replies (inst_id is not null)
            // @deprecated This implementation only shows student's own messages where student_id matches,
            // @deprecated but instructor messages have student_id = NULL, so students see NO replies.
            // @deprecated
            // @deprecated For correct implementation, see: App\Classes\MiscQueries::RecentChatMessages()
            return ChatLog::where('course_date_id', $course_date_id)
                ->whereNull('hidden_at')
                ->where('student_id', $user_id)
                ->orderBy('id', 'desc')
                ->limit($message_limit)
                ->get()
                ->reverse();
        }
    }
}
