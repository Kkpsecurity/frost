<?php

declare(strict_types=1);

namespace App\Classes\Frost;

use Illuminate\Support\Collection;

use  App\Services\RCache;

use App\Models\Course;
use App\Models\ChatLog;
use App\Models\InstUnit;
use App\Models\CourseDate;


class MiscQueries
{


    /**
     * Get CourseDates starting this month
     *
     * @param   Course      $Course
     * @return  Collection  [Eloquent]
     */
    public static function CalenderDates(Course $Course): \Illuminate\Database\Eloquent\Collection
    {

        return CourseDate::where('is_active', true)
            ->where('starts_at', '>=', date('Y-m-01'))
            ->whereIn('course_unit_id', $Course->GetCourseUnits()->pluck('id'))
            ->orderBy('starts_at')
            ->get();
    }


    /**
     * Get Recent ChatLog records
     *
     * @param   int         $course_date_id
     * @param   int         $user_id
     * @return  Collection
     */
    public static function RecentChatMessages(int $course_date_id, int $user_id): Collection
    {

        $message_limit = 50;

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
            // Student privacy rule:
            // Student only sees the 1:1 thread where student_id matches.
            // Instructor messages must be stored with the same student_id to appear.
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
