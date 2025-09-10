<?php
namespace App\Http\Controllers\React\Traits;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

use App\Models\CourseAuth;
use App\Models\SelfStudyLesson;


trait SelfStudyTrait
{


    public function SelfStudyLessons(int|CourseAuth $CourseAuth_or_id): Collection
    {

        $CourseAuth = $this->_GetCourseAuth($CourseAuth_or_id);
        $Course = $CourseAuth->GetCourse();
        $Lessons = $Course->GetLessons();
        $PCLCache = $CourseAuth->PCLCache();

        foreach ($Lessons as $Lesson) {
            $Lesson->is_completed = array_key_exists( $Lesson->id, $PCLCache );
        }

        return $Lessons;

    }


    public function SelfStudyUpdateTime(int|CourseAuth $CourseAuth_or_id, int $lesson_id, int $seconds): Collection
    {

        $CourseAuth = $this->_GetCourseAuth($CourseAuth_or_id);

        $SelfStudyLesson = SelfStudyLesson::updateOrCreate([
            'course_auth_id' => $CourseAuth->id,
            'lesson_id' => $lesson_id,
        ]);


        //
        // don't update record if already completed
        //

        if (!$SelfStudyLesson->completed_at) {

            $SelfStudyLesson->increment('seconds_viewed', $seconds);

            if ($SelfStudyLesson->seconds_viewed >= $SelfStudyLesson->GetLesson()->SelfStudyMinSeconds()) {

                $SelfStudyLesson->pgtouch('completed_at');

                $CourseAuth->PCLCache(true);

            }

        }


        return $this->SelfStudyLessons($CourseAuth);

    }


    private function _GetCourseAuth(int|CourseAuth $CourseAuth_or_id): CourseAuth
    {

        if (is_a($CourseAuth_or_id, CourseAuth::class)) {
            return $CourseAuth_or_id;
        }

        return CourseAuth::findOrFail($CourseAuth_or_id);

    }


}
