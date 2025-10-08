<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\AttendanceService;
use App\Models\StudentUnit;
use App\Models\CourseDate;
use App\Models\InstUnit;
use App\Models\StudentActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Carbon\Carbon;
use Exception;

class ClassroomOnboardingController extends Controller
{
    protected AttendanceService $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->middleware('auth');
        $this->attendanceService = $attendanceService;
    }

    /**
     * Check if student needs attendance marking for active class today.
     */
    public function checkAttendanceRequired(Request $request): JsonResponse
    {
        try {
            $student = Auth::user();
            $now    = now();
            $today  = $now->toDateString();

            // Active CourseDate window (now between starts_at and ends_at)
            $courseDate = CourseDate::whereDate('starts_at', $today)
                ->whereTime('starts_at', '<=', $now->toTimeString())
                ->whereTime('ends_at', '>=', $now->toTimeString())
                ->first();

            if (!$courseDate) {
                return response()->json([
                    'attendance_required' => false,
                    'message' => 'No active class today',
                ]);
            }

            // Enrollment check
            $courseAuth = $student->activeCourseAuths()
                ->where('course_id', $courseDate->course_id)
                ->first();

            if (!$courseAuth) {
                return response()->json([
                    'attendance_required' => false,
                    'message' => 'No enrollment found for this course',
                ]);
            }

            // Already has a StudentUnit for this CourseDate?
            $existingStudentUnit = StudentUnit::where('course_auth_id', $courseAuth->id)
                ->where('course_date_id', $courseDate->id)
                ->first();

            if ($existingStudentUnit) {
                return response()->json([
                    'attendance_required' => false,
                    'message' => 'Attendance already marked for this session',
                    'student_unit_id' => $existingStudentUnit->id,
                ]);
            }

            // Require active instructor session
            $activeInstUnit = InstUnit::where('course_date_id', $courseDate->id)
                ->whereNull('ended_at')
                ->first();

            if (!$activeInstUnit) {
                return response()->json([
                    'attendance_required' => false,
                    'message' => 'Class not started by instructor',
                ]);
            }

            return response()->json([
                'attendance_required' => true,
                'course_date_id' => $courseDate->id,
                'attendance_url' => route('classroom.attendance.mark', ['courseDate' => $courseDate->id]),
                'message' => 'Active class found — attendance required',
            ]);
        } catch (Exception $e) {
            Log::error('Attendance check error', ['error' => $e->getMessage()]);
            return response()->json([
                'attendance_required' => false,
                'error' => 'Error checking attendance: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show attendance marking page.
     */
    public function showAttendance(int $studentUnitId): View
    {
        $user = Auth::user();

        $studentUnit = StudentUnit::with([
            'CourseAuth.Course',
            'CourseDate.Course',
            'CourseDate.InstUnits' => fn ($q) => $q->whereNull('ended_at'),
        ])->findOrFail($studentUnitId);

        if ($studentUnit->CourseAuth->user_id !== $user->id) {
            abort(403, 'Unauthorized access');
        }

        $course         = $studentUnit->CourseDate->Course ?? $studentUnit->CourseAuth->Course;
        $activeInstUnit = $studentUnit->CourseDate->InstUnits->first();

        return view('student.attendance.mark', [
            'courseDate'  => $studentUnit->CourseDate,
            'course'      => $course,
            'studentUnit' => $studentUnit,
            'instUnit'    => $activeInstUnit,
        ]);
    }

    /**
     * Mark student attendance and redirect to onboarding.
     */
    public function markAttendance(Request $request, int $studentUnitId): JsonResponse
    {
        $user = Auth::user();

        try {
            $studentUnit = StudentUnit::with(['CourseAuth'])->findOrFail($studentUnitId);

            if ($studentUnit->CourseAuth->user_id !== $user->id) {
                return response()->json(['success' => false, 'message' => 'Unauthorized access'], 403);
            }

            $instUnit = $this->getActiveInstUnit($studentUnit);

            // Record attendance against the CourseDate ID (consistent with service signature)
            $result = $this->attendanceService->recordOfflineAttendance(
                $user,
                $studentUnit->course_date_id,
                [
                    'auto_created'  => false,
                    'created_from'  => 'attendance_mark',
                    'inst_unit_id'  => $instUnit?->id,
                    'student_unit_id' => $studentUnit->id,
                ]
            );

            if (!($result['success'] ?? false)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to record attendance',
                ], 500);
            }

            StudentActivity::create([
                'course_auth_id' => $studentUnit->CourseAuth->id,
                'student_unit_id'=> $studentUnit->id,
                'inst_unit_id'   => $instUnit?->id,
                'action'         => 'attendance_marked',
            ]);

            return response()->json([
                'success'      => true,
                'message'      => 'Attendance marked successfully',
                'redirect_url' => route('classroom.onboarding', $studentUnit->id),
            ]);
        } catch (Exception $e) {
            Log::error('Attendance marking error', [
                'user_id' => $user->id,
                'student_unit_id' => $studentUnitId,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['success' => false, 'message' => 'Failed to mark attendance'], 500);
        }
    }

    /**
     * Display onboarding page.
     */
    public function show(int $studentUnitId): View
    {
        $user = Auth::user();

        $studentUnit = StudentUnit::with([
            'CourseAuth.Course',
            'CourseDate.Course',
            'CourseDate.InstUnits' => fn ($q) => $q->whereNull('ended_at'),
        ])->findOrFail($studentUnitId);

        if ($studentUnit->CourseAuth->user_id !== $user->id) {
            abort(403, 'Unauthorized access');
        }

        $course         = $studentUnit->CourseDate->Course ?? $studentUnit->CourseAuth->Course;
        $activeInstUnit = $studentUnit->CourseDate->InstUnits->first();
        $instructor     = $activeInstUnit?->Instructor;

        // Track onboarding start once
        $alreadyTracked = StudentActivity::where('student_unit_id', $studentUnit->id)
            ->where('action', 'onboarding_started')
            ->exists();

        if (!$alreadyTracked) {
            StudentActivity::create([
                'course_auth_id' => $studentUnit->CourseAuth->id,
                'student_unit_id'=> $studentUnit->id,
                'inst_unit_id'   => $activeInstUnit?->id,
                'action'         => 'onboarding_started',
            ]);
        }

        $onboardingData = [
            'student_unit'    => $studentUnit,
            'course'          => $course,
            'instructor'      => $instructor,
            'course_date'     => $studentUnit->CourseDate,
            'inst_unit'       => $activeInstUnit,
            'student'         => $user,
            'onboarding_steps'=> $this->getOnboardingSteps($studentUnit),
        ];

        return view('student.onboarding.index', $onboardingData);
    }

    /**
     * Accept student agreement (Step 1).
     */
    public function acceptAgreement(Request $request, int $studentUnitId): JsonResponse
    {
        $user = Auth::user();

        try {
            $studentUnit = StudentUnit::with('CourseAuth')->findOrFail($studentUnitId);
            if ($studentUnit->CourseAuth->user_id !== $user->id) {
                return response()->json(['success' => false, 'message' => 'Unauthorized access'], 403);
            }

            StudentActivity::create([
                'course_auth_id' => $studentUnit->CourseAuth->id,
                'student_unit_id'=> $studentUnit->id,
                'inst_unit_id'   => $this->getActiveInstUnitId($studentUnit),
                'action'         => 'agreement_accepted',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Agreement accepted successfully',
                'next_step' => 'rules',
            ]);
        } catch (Exception $e) {
            Log::error('Agreement acceptance error', [
                'user_id' => $user->id,
                'student_unit_id' => $studentUnitId,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['success' => false, 'message' => 'Failed to accept agreement'], 500);
        }
    }

    /**
     * Acknowledge classroom rules (Step 2).
     */
    public function acknowledgeRules(Request $request, int $studentUnitId): JsonResponse
    {
        $user = Auth::user();

        try {
            $studentUnit = StudentUnit::with('CourseAuth')->findOrFail($studentUnitId);
            if ($studentUnit->CourseAuth->user_id !== $user->id) {
                return response()->json(['success' => false, 'message' => 'Unauthorized access'], 403);
            }

            StudentActivity::create([
                'course_auth_id' => $studentUnit->CourseAuth->id,
                'student_unit_id'=> $studentUnit->id,
                'inst_unit_id'   => $this->getActiveInstUnitId($studentUnit),
                'action'         => 'rules_acknowledged',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Rules acknowledged successfully',
                'next_step' => 'identity',
            ]);
        } catch (Exception $e) {
            Log::error('Rules acknowledgment error', [
                'user_id' => $user->id,
                'student_unit_id' => $studentUnitId,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['success' => false, 'message' => 'Failed to acknowledge rules'], 500);
        }
    }

    /**
     * Complete identity verification (Step 3).
     */
    public function verifyIdentity(Request $request, int $studentUnitId): JsonResponse
    {
        $user = Auth::user();

        try {
            $request->validate([
                'id_verified'    => 'required|boolean',
                'headshot_taken' => 'required|boolean',
            ]);

            $studentUnit = StudentUnit::with('CourseAuth')->findOrFail($studentUnitId);
            if ($studentUnit->CourseAuth->user_id !== $user->id) {
                return response()->json(['success' => false, 'message' => 'Unauthorized access'], 403);
            }

            StudentActivity::create([
                'course_auth_id' => $studentUnit->CourseAuth->id,
                'student_unit_id'=> $studentUnit->id,
                'inst_unit_id'   => $this->getActiveInstUnitId($studentUnit),
                'action'         => 'identity_verified',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Identity verified successfully',
                'next_step' => 'entry',
            ]);
        } catch (Exception $e) {
            Log::error('Identity verification error', [
                'user_id' => $user->id,
                'student_unit_id' => $studentUnitId,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['success' => false, 'message' => 'Failed to verify identity'], 500);
        }
    }

    /**
     * Complete onboarding and enter classroom (Step 4).
     */
    public function enterClassroom(Request $request, int $studentUnitId): JsonResponse
    {
        $user = Auth::user();

        try {
            $studentUnit = StudentUnit::with('CourseAuth')->findOrFail($studentUnitId);
            if ($studentUnit->CourseAuth->user_id !== $user->id) {
                return response()->json(['success' => false, 'message' => 'Unauthorized access'], 403);
            }

            $studentUnit->update(['completed_at' => Carbon::now()]);

            $instUnitId = $this->getActiveInstUnitId($studentUnit);

            StudentActivity::create([
                'course_auth_id' => $studentUnit->CourseAuth->id,
                'student_unit_id'=> $studentUnit->id,
                'inst_unit_id'   => $instUnitId,
                'action'         => 'onboarding_completed',
            ]);

            StudentActivity::create([
                'course_auth_id' => $studentUnit->CourseAuth->id,
                'student_unit_id'=> $studentUnit->id,
                'inst_unit_id'   => $instUnitId,
                'action'         => 'classroom_entered',
            ]);

            Log::info('Student completed onboarding and entered classroom', [
                'user_id' => $user->id,
                'student_unit_id' => $studentUnit->id,
                'course_date_id' => $studentUnit->course_date_id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Onboarding completed successfully',
                'redirect_url' => route('classroom.dashboard'),
            ]);
        } catch (Exception $e) {
            Log::error('Classroom entry error', [
                'user_id' => $user->id,
                'student_unit_id' => $studentUnitId,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['success' => false, 'message' => 'Failed to enter classroom'], 500);
        }
    }

    /**
     * Build onboarding steps from activity log.
     */
    private function getOnboardingSteps(StudentUnit $studentUnit): array
    {
        $activities = StudentActivity::where('student_unit_id', $studentUnit->id)
            ->whereIn('action', ['agreement_accepted', 'rules_acknowledged', 'identity_verified', 'onboarding_completed'])
            ->get()
            ->keyBy('action');

        return [
            'agreement' => [
                'completed'    => isset($activities['agreement_accepted']),
                'completed_at' => $activities['agreement_accepted']->created_at ?? null,
                'title'        => 'Student Agreement',
                'description'  => 'Accept course terms and expectations',
            ],
            'rules' => [
                'completed'    => isset($activities['rules_acknowledged']),
                'completed_at' => $activities['rules_acknowledged']->created_at ?? null,
                'title'        => 'Classroom Rules',
                'description'  => 'Acknowledge classroom conduct and participation rules',
            ],
            'identity' => [
                'completed'    => isset($activities['identity_verified']),
                'completed_at' => $activities['identity_verified']->created_at ?? null,
                'title'        => 'Identity Verification',
                'description'  => 'Verify ID and capture daily headshot',
            ],
            'entry' => [
                'completed'    => isset($activities['onboarding_completed']) || !is_null($studentUnit->completed_at),
                'completed_at' => $activities['onboarding_completed']->created_at ?? $studentUnit->completed_at,
                'title'        => 'Enter Classroom',
                'description'  => 'Complete setup and join the class',
            ],
        ];
    }

    /**
     * Get active InstUnit model for this StudentUnit's CourseDate.
     */
    private function getActiveInstUnit(StudentUnit $studentUnit): ?InstUnit
    {
        return InstUnit::where('course_date_id', $studentUnit->course_date_id)
            ->whereNull('ended_at')
            ->first();
    }

    /**
     * Get active InstUnit ID for activity logging.
     */
    private function getActiveInstUnitId(StudentUnit $studentUnit): ?int
    {
        return InstUnit::where('course_date_id', $studentUnit->course_date_id)
            ->whereNull('ended_at')
            ->value('id');
    }
}
