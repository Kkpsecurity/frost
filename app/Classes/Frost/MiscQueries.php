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

        if ($user_id == InstUnit::firstWhere('course_date_id', $course_date_id)->created_by) {

            return ChatLog::where('course_date_id', $course_date_id)
                ->whereNull('hidden_at')
                ->orderBy('id', 'desc')
                ->limit($message_limit)
                ->get()
                ->reverse();
        } else {

            return ChatLog::where('course_date_id', $course_date_id)
                ->whereNull('hidden_at')
                ->where(function ($query) use ($user_id) {
                    $query->where('student_id', $user_id)
                        ->orWhereNotNull('inst_id');
                })
                ->orderBy('id', 'desc')
                ->limit($message_limit)
                ->get()
                ->reverse();
        }
    }
}
