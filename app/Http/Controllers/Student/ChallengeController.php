<?php

namespace App\Http\Controllers\Student;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use App\Http\Controllers\Controller;
use App\Models\Challenge;
use App\Models\StudentLesson;

/**
 * ChallengeController
 *
 * Handles student responses to participation challenges during live lessons.
 * Part of Step 2 in Challenge System Integration.
 */
class ChallengeController extends Controller
{
    /**
     * Respond to a challenge
     *
     * Student confirms their participation by completing the challenge.
     * This marks the challenge as completed and prevents lesson failure.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function respond(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 401);
            }

            // Validate request
            $validator = Validator::make($request->all(), [
                'challenge_id' => 'required|integer|exists:challenges,id',
                'completed' => 'required|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $challengeId = $request->input('challenge_id');
            $completed = $request->input('completed');

            // Load challenge with relationships
            $challenge = Challenge::with('StudentLesson.StudentUnit.CourseAuth')
                ->find($challengeId);

            if (!$challenge) {
                return response()->json([
                    'success' => false,
                    'message' => 'Challenge not found',
                ], 404);
            }

            // Verify this challenge belongs to the current user
            $studentLesson = $challenge->StudentLesson;
            if (!$studentLesson) {
                Log::error('Challenge has no StudentLesson', [
                    'challenge_id' => $challengeId,
                    'user_id' => $user->id,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Invalid challenge',
                ], 403);
            }

            $studentUnit = $studentLesson->StudentUnit;
            if (!$studentUnit || !$studentUnit->CourseAuth) {
                Log::error('Challenge StudentLesson has no valid StudentUnit/CourseAuth', [
                    'challenge_id' => $challengeId,
                    'student_lesson_id' => $studentLesson->id,
                    'user_id' => $user->id,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Invalid challenge',
                ], 403);
            }

            // Verify ownership
            if ($studentUnit->CourseAuth->user_id !== $user->id) {
                Log::warning('Challenge response attempt by wrong user', [
                    'challenge_id' => $challengeId,
                    'challenge_user_id' => $studentUnit->CourseAuth->user_id,
                    'requesting_user_id' => $user->id,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized - This challenge belongs to another student',
                ], 403);
            }

            // Check if challenge already completed or failed
            if ($challenge->completed_at) {
                return response()->json([
                    'success' => true,
                    'message' => 'Challenge already completed',
                    'challenge' => [
                        'id' => $challenge->id,
                        'status' => 'completed',
                        'completed_at' => $challenge->completed_at->toISOString(),
                    ],
                ]);
            }

            if ($challenge->failed_at) {
                return response()->json([
                    'success' => false,
                    'message' => 'Challenge already failed - Cannot complete expired challenge',
                    'challenge' => [
                        'id' => $challenge->id,
                        'status' => 'failed',
                        'failed_at' => $challenge->failed_at->toISOString(),
                    ],
                ], 400);
            }

            // Check if challenge has expired
            if (now()->greaterThan($challenge->expires_at)) {
                // Auto-mark as failed
                $challenge->MarkFailed();

                Log::info('Challenge expired during response attempt', [
                    'challenge_id' => $challengeId,
                    'user_id' => $user->id,
                    'expires_at' => $challenge->expires_at->toISOString(),
                    'is_final' => $challenge->is_final,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Challenge has expired',
                    'challenge' => [
                        'id' => $challenge->id,
                        'status' => 'expired',
                        'expires_at' => $challenge->expires_at->toISOString(),
                        'is_final' => (bool) $challenge->is_final,
                    ],
                ], 400);
            }

            // Mark challenge as completed
            if ($completed) {
                $challenge->MarkCompleted();

                Log::info('Challenge completed by student', [
                    'challenge_id' => $challengeId,
                    'user_id' => $user->id,
                    'student_lesson_id' => $studentLesson->id,
                    'is_final' => $challenge->is_final,
                    'is_eol' => $challenge->is_eol,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Challenge completed successfully',
                    'challenge' => [
                        'id' => $challenge->id,
                        'status' => 'completed',
                        'completed_at' => $challenge->fresh()->completed_at->toISOString(),
                        'is_final' => (bool) $challenge->is_final,
                        'is_eol' => (bool) $challenge->is_eol,
                    ],
                ]);
            } else {
                // Student explicitly failed/dismissed challenge
                // This shouldn't normally happen with slider UI, but handle it
                return response()->json([
                    'success' => false,
                    'message' => 'Challenge not completed',
                ], 400);
            }
        } catch (Exception $e) {
            Log::error('Challenge response error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'challenge_id' => $request->input('challenge_id'),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your response',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /*
     * DEPRECATED: Challenge history is now included in the student poll data
     * See StudentDashboardController::getStudentPollData() for the current implementation
     *
    public function history(Request $request)
    {
        try {
            $studentUnitId = $request->input('student_unit_id');
            $courseAuthId = $request->input('course_auth_id');

            // If no student_unit_id provided, find it
            if (!$studentUnitId) {
                // Prefer course_auth_id if provided (from active classroom)
                if ($courseAuthId) {
                    $courseAuth = \App\Models\CourseAuth::where('id', $courseAuthId)
                        ->where('user_id', auth()->id())
                        ->first();

                    if (!$courseAuth) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Course enrollment not found or unauthorized',
                        ], 403);
                    }
                } else {
                    // Fall back to most recent course auth
                    $courseAuth = \App\Models\CourseAuth::where('user_id', auth()->id())
                        ->orderBy('id', 'desc')
                        ->first();

                    if (!$courseAuth) {
                        return response()->json([
                            'success' => false,
                            'message' => 'No course enrollment found',
                        ], 404);
                    }
                }

                $studentUnit = \App\Models\StudentUnit::where('course_auth_id', $courseAuth->id)
                    ->orderBy('id', 'desc')
                    ->first();

                if (!$studentUnit) {
                    return response()->json([
                        'success' => true,
                        'challenges' => [],
                        'stats' => ['completed' => 0, 'failed' => 0, 'expired' => 0],
                        'message' => 'No student unit found yet',
                    ]);
                }

                $studentUnitId = $studentUnit->id;
            } else {
                // Get the student unit and verify ownership
                $studentUnit = \App\Models\StudentUnit::with(['CourseAuth.user'])
                    ->find($studentUnitId);

                if (!$studentUnit) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Student unit not found',
                    ], 404);
                }

                // Verify the student owns this unit
                if ($studentUnit->CourseAuth->user_id !== auth()->id()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized access',
                    ], 403);
                }
            }

            // Get all student lessons for this unit
            $studentLessons = \App\Models\StudentLesson::where('student_unit_id', $studentUnitId)
                ->pluck('id');

            // Get all challenges for these lessons
            $challenges = \App\Models\Challenge::with(['StudentLesson.Lesson'])
                ->whereIn('student_lesson_id', $studentLessons)
                ->where(function ($query) {
                    $query->whereNotNull('completed_at')
                        ->orWhereNotNull('failed_at')
                        ->orWhere('expires_at', '<', now());
                })
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get();

            // Calculate statistics
            $stats = [
                'completed' => $challenges->where('completed_at', '!=', null)->count(),
                'failed' => $challenges->where('failed_at', '!=', null)->count(),
                'expired' => $challenges->where('expires_at', '<', now())
                    ->whereNull('completed_at')
                    ->whereNull('failed_at')
                    ->count(),
            ];

            return response()->json([
                'success' => true,
                'challenges' => $challenges->map(function ($challenge) {
                    return [
                        'id' => $challenge->id,
                        'created_at' => $challenge->created_at,
                        'expires_at' => $challenge->expires_at,
                        'completed_at' => $challenge->completed_at,
                        'failed_at' => $challenge->failed_at,
                        'is_final' => $challenge->is_final,
                        'is_eol' => $challenge->is_eol,
                        'student_lesson' => [
                            'id' => $challenge->StudentLesson->id,
                            'lesson' => [
                                'id' => $challenge->StudentLesson->Lesson->id,
                                'name' => $challenge->StudentLesson->Lesson->name,
                            ],
                        ],
                    ];
                }),
                'stats' => $stats,
            ]);
        } catch (\Exception $e) {
            \Log::error('Challenge history error: ' . $e->getMessage(), [
                'student_unit_id' => $request->input('student_unit_id'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching challenge history',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    */
}
