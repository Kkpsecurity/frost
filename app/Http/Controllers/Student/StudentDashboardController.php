<?php

namespace App\Http\Controllers\Student;

use Exception;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\RedirectResponse;

use App\Models\CourseAuth;
use App\Models\CourseDate;
use App\Models\CourseUnit;
use App\Models\StudentUnit;
use App\Models\StudentActivity;
use App\Models\User;
use App\Classes\ClassroomQueries;
use App\Traits\PageMetaDataTrait;
use App\Http\Controllers\Controller;

use App\Services\AttendanceService;
use App\Services\IdVerificationService;
use App\Services\StudentActivityTracker;
use App\Services\StudentDashboardService;
use App\Services\StudentDataArrayService;
use App\Services\ClassroomDataArrayService;
use App\Services\ClassroomDashboardService;
use App\Services\StudentUnitService;
use App\Services\SelfStudyLessonService;

// New refactored services
use App\Services\Student\StudentAttendanceService;
use App\Services\Student\StudentVerificationService;
use App\Services\Student\StudentLessonService;
use App\Services\Student\StudentClassroomService;
use App\Traits\StudentDataHelper;

class StudentDashboardController extends Controller
{
    use PageMetaDataTrait;
    use StudentDataHelper;

    /**
     * Poll: Get student-specific data
     * Called every 5 seconds from StudentDataLayer.tsx
     */
    public function getStudentPollData(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $courseAuthId = $request->query('course_auth_id');

            if (!$courseAuthId || !$user) {
                return response()->json([
                    'success' => false,
                    'data' => [
                        'student' => null,
                        'courses' => [],
                        'progress' => null,
                        'notifications' => [],
                        'assignments' => [],
                    ],
                ]);
            }

            // Get student's course enrollment
            $courseAuth = CourseAuth::find($courseAuthId);
            if (!$courseAuth || $courseAuth->user_id !== $user->id) {
                return response()->json(['success' => false, 'data' => null]);
            }

            // Return student data
            return response()->json([
                'success' => true,
                'data' => [
                    'student' => [
                        'id' => $user->id,
                        'name' => $user->fname . ' ' . $user->lname,
                        'email' => $user->email,
                    ],
                    'courses' => [$courseAuth->course],
                    'progress' => null,
                    'notifications' => [],
                    'assignments' => [],
                ],
            ]);
        } catch (Exception $e) {
            Log::error('Student poll data error: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Poll: Get classroom data for active course date
     * Called every 5 seconds from StudentDataLayer.tsx
     */
    public function getClassroomPollData(Request $request): JsonResponse
    {
        try {
            $courseDateId = $request->query('course_date_id');
            $user = Auth::user();

            if (!$courseDateId) {
                return response()->json([
                    'success' => false,
                    'courseDate' => null,
                    'courseUnit' => null,
                    'course' => null,
                    'lessons' => [],
                    'instUnit' => null,
                    'config' => [],
                ]);
            }

            // Load course date with relationships
            $courseDate = CourseDate::with([
                'course',
                'course.courseUnit',
                'course.courseUnit.courseUnitLessons',
                'instUnit',
                'instUnit.instLessons',
                'studentUnits',
            ])->find($courseDateId);

            if (!$courseDate) {
                return response()->json([
                    'success' => false,
                    'courseDate' => null,
                    'courseUnit' => null,
                    'course' => null,
                    'lessons' => [],
                    'instUnit' => null,
                    'config' => [],
                ]);
            }

            // Return classroom data
            return response()->json([
                'success' => true,
                'courseDate' => $courseDate,
                'courseUnit' => $courseDate->course?->courseUnit,
                'course' => $courseDate->course,
                'lessons' => $courseDate->course?->courseUnit?->courseUnitLessons ?? [],
                'instUnit' => $courseDate->instUnit,
                'config' => [],
            ]);
        } catch (Exception $e) {
            Log::error('Classroom poll data error: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Show main student dashboard
     */
    public function dashboard(Request $request): View
    {
        $user = Auth::user();
        return view('dashboards.student.index', [
            'user' => $user,
        ]);
    }
}
