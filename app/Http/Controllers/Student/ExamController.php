<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Classes\ExamAuthObj;
use App\Models\CourseAuth;
use App\Models\ExamAuth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
                'exam',
                'course',
                'CourseAuth',
                'questions' => function ($query) {
                    $query->with('lesson');
                }
            ]);

            return response()->json([
                'success' => true,
                'exam_auth' => [
                    'id' => $examAuth->id,
                    'course_auth_id' => $examAuth->course_auth_id,
                    'completed_at' => $examAuth->completed_at,
                    'expires_at' => $examAuth->expires_at,
                    'score' => $examAuth->score,
                    'is_passed' => $examAuth->is_passed,
                    'passed' => $examAuth->is_passed,
                    'answers' => $examAuth->answers,
                    'incorrect' => $examAuth->incorrect,
                    'next_attempt_at' => $examAuth->next_attempt_at,
                    'exam' => $examAuth->exam ? [
                        'id' => $examAuth->exam->id,
                        'num_questions' => $examAuth->exam->num_questions,
                        'num_to_pass' => $examAuth->exam->num_to_pass,
                        'policy_expire_seconds' => $examAuth->exam->policy_expire_seconds,
                    ] : null,
                    'course' => $examAuth->course ? [
                        'id' => $examAuth->course->id,
                        'title' => $examAuth->course->title,
                    ] : null,
                    'questions' => $examAuth->questions->map(function ($question) {
                        return [
                            'id' => $question->id,
                            'question' => $question->question,
                            'answer_1' => $question->answer_1,
                            'answer_2' => $question->answer_2,
                            'answer_3' => $question->answer_3,
                            'answer_4' => $question->answer_4,
                            'answer_5' => $question->answer_5,
                            'correct_answer' => $question->correct,
                            'lesson_id' => $question->lesson_id,
                            'lesson' => $question->lesson ? [
                                'id' => $question->lesson->id,
                                'title' => $question->lesson->title,
                            ] : null,
                        ];
                    }),
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

            $courseAuth = CourseAuth::findOrFail($courseAuthId);

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
                return response()->json([
                    'success' => false,
                    'error' => 'Not ready for exam. Complete all lessons first.',
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
}
