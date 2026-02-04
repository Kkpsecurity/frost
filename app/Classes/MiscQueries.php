<?php

declare(strict_types=1);

namespace App\Classes;

#use Auth;
#use DB;
use Illuminate\Support\Collection;

use RCache;
use App\Models\ChatLog;
use App\Models\Course;
use App\Models\CourseDate;
use App\Models\InstUnit;
#use App\Models\User;
#use KKP\Laravel\PgTk;


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

    // The frontend month grid includes spillover days from the previous month.
    // Include at least the previous month in the query so those days can display events.
    $fromUtc = \Illuminate\Support\Carbon::now('UTC')->startOfMonth()->subMonth();

    return CourseDate::query()
      ->where('starts_at', '>=', $fromUtc->toIso8601String())
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

      // Student privacy rule (hub-and-spoke model):
      // - Students see their own messages (student_id matches, inst_id is null)
      // - Students see ALL instructor replies (inst_id is not null)
      // - Students DO NOT see other students' messages
      return ChatLog::where('course_date_id', $course_date_id)
        ->whereNull('hidden_at')
        ->where(function ($query) use ($user_id) {
          // Show student's own messages
          $query->where(function ($q) use ($user_id) {
            $q->where('student_id', $user_id)
              ->whereNull('inst_id');
          })
            // OR show all instructor messages
            ->orWhereNotNull('inst_id');
        })
        ->orderBy('id', 'desc')
        ->limit($message_limit)
        ->get()
        ->reverse();
    }
  }
}
