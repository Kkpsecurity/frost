<?php

namespace App\Services;

use App\Classes\ChatLogCache;
use App\Classes\ClassroomQueries;
use App\Classes\VideoCallRequest;
use App\Helpers\Helpers;
use App\Models\InstLesson;
use App\Models\StudentUnit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;

class ClassRoomServices
{

    /**
     * Validate the Initial Session
     * This has no Polling however we do make request back
     * onFocus to keep and stale data upto date
     * stale time is for 30sec
     */
    protected function assignedInstructor($CourseDate)
    {
        $instructor = null;

        if ($CourseDate && $CourseDate->InstUnit && $CourseDate->InstUnit->created_by) {
            $instructor = $CourseDate->InstUnit->GetCreatedBy();
            $instructor->avatar = $instructor->getAvatar('thumb');

            $authenticatedUserId = Auth::id();
            $instructorCreatorId = $CourseDate->InstUnit->created_by;

            $instructor['userRole'] = ($authenticatedUserId === $instructorCreatorId) ? 'instructor' : 'assistant';
            $instructor['instUnit'] = $CourseDate->InstUnit->toArray();

            if ($zoom = $CourseDate->InstUnit->GetCourse()->ZoomCreds) {
                $zoom->zoom_passcode = Crypt::decrypt($zoom->zoom_passcode);
                $zoom->zoom_password = Crypt::decrypt($zoom->zoom_password);
                $instructor->zoom_payload = $zoom->toArray();
            }
        }

        return $instructor;
    }
    public function getAssistant($assistant_id)
    {
        if (!$assistant_id) {
            return response()->json([
                'success' => false,
                'message' => "No Assistant Found"
            ]);
        }

        $assistant = User::find($assistant_id);
        $assistant->avatar = $assistant->getAvatar('thumb');
        return response()->json($assistant);
    }

    public function getClassData($CourseDates)
    {
        $classData = []; // Initialize the array to hold class data

        foreach ($CourseDates as $index => $CourseDate) {

            if (
                $CourseDate->InstUnit && (
                    $CourseDate->InstUnit->created_by == auth()->user()->id ||
                    $CourseDate->InstUnit->assistant_id == auth()->user()->id
                )
            ) {
                // Initialize or reset the array for each course date that meets the condition
                $classData[$index] = [
                    'courseDate' => $CourseDate,
                    'instructor' => $this->assignedInstructor($CourseDate),
                    'assistant' => $this->getAssistant($CourseDate->InstUnit->assistant_id ?? null),
                    'instUnit' => $CourseDate->instUnit->toArray(),
                    'instUnitLesson' => ($instructorLesson = ClassroomQueries::ActiveInstLesson($CourseDate->instUnit)) ? $instructorLesson : null,
                    'instructorCanClose' => $instructorLesson ? $instructorLesson->instCanClose() : false,
                    'completedLessons' => InstLesson::select(['lesson_id', 'completed_at'])
                        ->where('inst_unit_id', $CourseDate->inst_unit_id)
                        ->where('completed_at', '!=', null)
                        ->get()
                        ->keyBy('lesson_id'),
                    'course' => $CourseDate->getCourse()->toArray(),
                    'courseUnitLessons' => $CourseDate->getCourseUnit()->getCourseUnitLessons(),
                    'lessons' => $CourseDate->getCourseUnit()->getLessons()->toArray(),
                    'isChatEnabled' => ChatLogCache::IsEnabled((int) $CourseDate->id),

                    'studentUnit' => ($studentUnits = StudentUnit::where('course_date_id', $CourseDate->id)->get()),

                    'totalStudentsCount' => $studentUnits->count(),
                    'completedStudentsCount' => $studentUnits->where('completed_at', '!=', null)->count(),
                    'callRequest' => VideoCallRequest::queue($CourseDate->id),
                    'appVersion' => Helpers::AppVersion(),
                ];

                // Special handling for instructorLesson's created_at modification
                if ($instructorLesson && auth()->user()->id == 2) {
                    $classData[$index]['instUnitLesson']->created_at = $instructorLesson->createdAt(); // Testing mode adjustment
                } elseif ($instructorLesson) {
                    $classData[$index]['instUnitLesson']->created_at = Carbon::parse($instructorLesson->created_at)->isoFormat('YYYY-MM-DD HH:mm:ss');
                }
            }
        }

        return $classData;
    }


}


