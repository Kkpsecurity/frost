<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Classes\ExamAuthObj;
use App\Models\CourseAuth;
use App\Models\ExamAuth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use KKP\Laravel\PgTk;

class ExamController extends Controller
{
    /**
     * Get exam auth data with questions
     */
    public function getExamAuth(ExamAuth $examAuth): JsonResponse
    {
        try {
            // Create ExamAuthObj - will validate ownership
            $ExamAuthObj = new ExamAuthObj($examAuth);

            // Load all related data
            $examAuth->load([
                'course',
                'CourseAuth.course.exam',
            ]);

            $questions = [];
            if (!empty($ExamAuthObj->ExamQuestions)) {
                $questions = collect($ExamAuthObj->ExamQuestions)
                    ->map(fn($q) => [
                        'id' => $q->id,
                        'lesson_id' => $q->lesson_id,
                        'question' => $q->question,
                        'answer_1' => $q->answer_1,
                        'answer_2' => $q->answer_2,
                        'answer_3' => $q->answer_3,
                        'answer_4' => $q->answer_4,
                        'answer_5' => $q->answer_5,
                    ])
                    ->values()
                    ->all();
            }

            return response()->json([
                'success' => true,
                'exam_auth' => [
                    'id' => $examAuth->id,
                    'course_auth_id' => $examAuth->course_auth_id,
                    'completed_at' => $examAuth->completed_at,
                    // Always return Unix seconds for frontend timer math
                    'expires_at' => $examAuth->expires_at ? Carbon::parse($examAuth->expires_at)->timestamp : null,
                    'score' => $examAuth->score,
                    'is_passed' => $examAuth->is_passed,
                    'passed' => $examAuth->is_passed,
                    'answers' => $examAuth->answers,
                    'incorrect' => $examAuth->incorrect,
                    'next_attempt_at' => $examAuth->next_attempt_at,
                    'exam' => $examAuth->CourseAuth && $examAuth->CourseAuth->course && $examAuth->CourseAuth->course->exam ? [
                        'id' => $examAuth->CourseAuth->course->exam->id,
                        'num_questions' => $examAuth->CourseAuth->course->exam->num_questions,
                        'num_to_pass' => $examAuth->CourseAuth->course->exam->num_to_pass,
                        'policy_expire_seconds' => $examAuth->CourseAuth->course->exam->policy_expire_seconds,
                    ] : null,
                    'course' => $examAuth->course ? [
                        'id' => $examAuth->course->id,
                        'title' => $examAuth->course->title,
                    ] : null,
                    'question_ids' => $examAuth->question_ids ?? [],
                    'questions' => $questions,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 403);
        }
    }

    /**
     * Begin a new exam (create ExamAuth)
     */
    public function beginExam(Request $request): JsonResponse
    {
        try {
            $courseAuthId = $request->input('course_auth_id');

            $courseAuth = CourseAuth::with('course')->findOrFail($courseAuthId);

            // Verify ownership
            if ($courseAuth->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Not authorized',
                ], 403);
            }

            // Check if course is active
            if (!$courseAuth->IsActive()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Course is no longer active',
                ], 400);
            }

            // Check for existing active exam
            if ($existingExam = $courseAuth->ActiveExamAuth()) {
                // Return existing exam
                return $this->getExamAuth($existingExam);
            }

            // Check if student is ready for exam
            if (!$courseAuth->ExamReady()) {
                $readiness = $courseAuth->ExamReadinessFailureReason();
                $message = 'Not ready for exam.';

                if (is_array($readiness) && isset($readiness['reason'])) {
                    switch ($readiness['reason']) {
                        case 'inactive':
                            $message = 'Course is no longer active.';
                            break;
                        case 'already_passed':
                            $message = 'You already passed this exam.';
                            break;
                        case 'cooldown':
                            $nextAttempt = $readiness['next_attempt_at'] ?? null;
                            if ($nextAttempt) {
                                $nextAttemptTime = Carbon::parse($nextAttempt);
                                $message = 'You can retake the exam on ' . $nextAttemptTime->toDayDateTimeString() . '.';
                            } else {
                                $message = 'You must wait before attempting the exam again.';
                            }
                            break;
                        case 'lessons':
                        default:
                            $message = 'Complete all lessons first.';
                            break;
                    }
                }

                return response()->json([
                    'success' => false,
                    'error' => $message,
                ], 400);
            }

            // Create new exam auth
            $examAuth = ExamAuth::create([
                'course_auth_id' => $courseAuth->id,
            ]);

            $examAuth->refresh();

            // Return the newly created exam
            return $this->getExamAuth($examAuth);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Start exam timer (called when user clicks "Begin Exam" on acknowledgement screen)
     */
    public function startExamTimer(ExamAuth $examAuth): JsonResponse
    {
        try {
            // Create ExamAuthObj - will validate ownership
            $ExamAuthObj = new ExamAuthObj($examAuth);

            // Check if timer already started
            if ($examAuth->expires_at) {
                return response()->json([
                    'success' => true,
                    'expires_at' => Carbon::parse($examAuth->expires_at)->timestamp,
                ]);
            }

            // Timer must be based on actual exam start, not attempt creation.
            // We re-base created_at here so ExamAuth::MakeExpiresAt() and wait policies are consistent.
            $now = Carbon::parse(PgTk::now());

            $exam = $examAuth->GetExam();
            $expiresAt = null;
            if ($exam && $exam->policy_expire_seconds) {
                $expiresAt = $now->copy()->addSeconds($exam->policy_expire_seconds);
            }

            $examAuth->forceFill([
                'created_at' => $now,
                'expires_at' => $expiresAt,
            ])->save();

            return response()->json([
                'success' => true,
                'expires_at' => $examAuth->expires_at ? Carbon::parse($examAuth->expires_at)->timestamp : null,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 403);
        }
    }

    /**
     * Submit and score exam
     */
    public function submitExam(Request $request, ExamAuth $examAuth): JsonResponse
    {
        try {
            $ExamAuthObj = new ExamAuthObj($examAuth);

            // Validate can score
            if (!$ExamAuthObj->ValidateCanScore(true)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Cannot score this exam. It may be expired or already completed.',
                ], 400);
            }

            // Convert answers array to Request format
            $answers = $request->input('answers', []);
            $requestData = [];

            foreach ($answers as $questionId => $answerValue) {
                $requestData["answer_{$questionId}"] = $answerValue;
            }

            // Create a new request with the formatted data
            $scoringRequest = new Request($requestData);

            // Score the exam
            $ExamAuthObj->Score($scoringRequest);

            // Return updated exam data
            return $this->getExamAuth($examAuth->refresh());
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all exam attempts for a course auth (for review)
     */
    public function getExamAttempts(Request $request): JsonResponse
    {
        try {
            $courseAuthId = $request->input('course_auth_id');

            if (!$courseAuthId) {
                return response()->json([
                    'success' => false,
                    'error' => 'course_auth_id is required',
                ], 400);
            }

            $user = auth()->user();
            $courseAuth = CourseAuth::with('course.exam')->where('id', $courseAuthId)
                ->where('user_id', $user->id)
                ->first();

            if (!$courseAuth) {
                return response()->json([
                    'success' => false,
                    'error' => 'Course authorization not found or access denied',
                ], 404);
            }

            // Get all exam attempts, ordered by most recent first
            $examAuths = ExamAuth::where('course_auth_id', $courseAuth->id)
                ->whereNull('hidden_at')
                ->whereNotNull('completed_at')
                ->orderBy('completed_at', 'DESC')
                ->get();

            $attempts = [];
            foreach ($examAuths as $examAuth) {
                try {
                    $ExamAuthObj = new ExamAuthObj($examAuth);

                    $questions = [];
                    if (!empty($ExamAuthObj->ExamQuestions)) {
                        $questions = collect($ExamAuthObj->ExamQuestions)
                            ->map(fn($q) => [
                                'id' => $q->id,
                                'lesson_id' => $q->lesson_id,
                                'question' => $q->question,
                                'answer_1' => $q->answer_1,
                                'answer_2' => $q->answer_2,
                                'answer_3' => $q->answer_3,
                                'answer_4' => $q->answer_4,
                                'answer_5' => $q->answer_5,
                                'correct' => $q->correct,
                            ])
                            ->values()
                            ->all();
                    }

                    $attempts[] = [
                        'id' => $examAuth->id,
                        'completed_at' => $examAuth->completed_at,
                        'score' => $examAuth->score,
                        'is_passed' => $examAuth->is_passed,
                        'answers' => $examAuth->answers,
                        'incorrect' => $examAuth->incorrect,
                        'question_ids' => $examAuth->question_ids ?? [],
                        'questions' => $questions,
                    ];
                } catch (\Exception $e) {
                    Log::warning("Failed to load exam attempt {$examAuth->id}: " . $e->getMessage());
                    continue;
                }
            }

            // Get exam config
            $exam = $courseAuth->course?->exam;
            $maxAttempts = 2; // TODO: Make this configurable
            $totalAttempts = count($attempts);
            $remainingAttempts = max(0, $maxAttempts - $totalAttempts);

            return response()->json([
                'success' => true,
                'attempts' => $attempts,
                'total_attempts' => $totalAttempts,
                'max_attempts' => $maxAttempts,
                'remaining_attempts' => $remainingAttempts,
                'exam' => $exam ? [
                    'id' => $exam->id,
                    'num_questions' => $exam->num_questions,
                    'num_to_pass' => $exam->num_to_pass,
                    'policy_expire_seconds' => $exam->policy_expire_seconds,
                ] : null,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get exam attempts: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reset/delete all exam attempts for a course auth (dev only)
     */
    public function resetExam(Request $request): JsonResponse
    {
        try {
            $courseAuthId = $request->input('course_auth_id');

            if (!$courseAuthId) {
                return response()->json([
                    'success' => false,
                    'error' => 'course_auth_id is required',
                ], 400);
            }

            $user = auth()->user();
            $courseAuth = CourseAuth::where('id', $courseAuthId)
                ->where('user_id', $user->id)
                ->first();

            if (!$courseAuth) {
                return response()->json([
                    'success' => false,
                    'error' => 'Course authorization not found or access denied',
                ], 404);
            }

            // Delete all exam attempts for this course auth
            $deletedCount = ExamAuth::where('course_auth_id', $courseAuth->id)->delete();

            // Also clear course completion state so the student can retake in dev/testing.
            // (Earlier timer bugs could auto-expire attempts and incorrectly fail the course.)
            $courseAuth->forceFill([
                'completed_at' => null,
                'is_passed' => false,
            ])->save();

            Log::info("Dev: Reset exam attempts for course_auth_id {$courseAuth->id}, deleted {$deletedCount} attempts");

            return response()->json([
                'success' => true,
                'message' => "Reset {$deletedCount} exam attempt(s)",
                'deleted_count' => $deletedCount,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to reset exam: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
