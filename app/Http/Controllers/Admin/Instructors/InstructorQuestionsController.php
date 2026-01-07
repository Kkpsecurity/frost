<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Instructors;

use App\Classes\ChatLogCache;
use App\Classes\ClassroomSessionModeCache;
use App\Http\Controllers\Controller;
use App\Models\AiChatLog;
use App\Models\ChatLog;
use App\Models\InstructorQuestion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InstructorQuestionsController extends Controller
{
    public function list(Request $request): JsonResponse
    {
        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $courseDateId = $request->query('course_date_id');
        $courseDateId = is_numeric($courseDateId) ? (int) $courseDateId : null;
        if (!$courseDateId) {
            return response()->json(['success' => true, 'questions' => [], 'mode' => ClassroomSessionModeCache::MODE_TEACHING]);
        }

        $mode = ClassroomSessionModeCache::Get($courseDateId);

        $questions = InstructorQuestion::query()
            ->where('course_date_id', $courseDateId)
            ->orderByDesc('id')
            ->limit(100)
            ->get()
            ->map(function (InstructorQuestion $q) {
                return [
                    'id' => (int) $q->id,
                    'student_id' => (int) $q->student_id,
                    'topic' => (string) $q->topic,
                    'urgency' => (string) $q->urgency,
                    'question' => (string) $q->question,
                    'status' => (string) $q->status,
                    'created_at' => optional($q->created_at)->format('c'),
                    'answer_visibility' => $q->answer_visibility,
                    'answer_text' => $q->answer_text,
                    'ai_status' => $q->ai_status,
                    'ai_confidence' => $q->ai_confidence,
                    'ai_sources' => $q->ai_sources,
                    'ai_answer_instructor' => $q->ai_answer_instructor,
                    'ai_answer_student' => $q->ai_answer_student,
                ];
            })
            ->toArray();

        return response()->json([
            'success' => true,
            'mode' => $mode,
            'questions' => $questions,
        ]);
    }

    public function cycleMode(Request $request): JsonResponse
    {
        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $validator = Validator::make($request->all(), [
            'course_date_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation failed: ' . $validator->errors()->first()], 422);
        }

        $courseDateId = (int) $request->input('course_date_id');
        $mode = ClassroomSessionModeCache::Cycle($courseDateId);

        // Keep legacy chat toggle in sync: only allow chat outside TEACHING.
        if ($mode === ClassroomSessionModeCache::MODE_TEACHING) {
            ChatLogCache::Disable($courseDateId);
        } else {
            ChatLogCache::Enable($courseDateId);
        }

        return response()->json(['success' => true, 'mode' => $mode]);
    }

    public function hold(Request $request, int $id): JsonResponse
    {
        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $q = InstructorQuestion::findOrFail($id);
        $q->status = 'held';
        $q->held_at = now();
        $q->save();

        return response()->json(['success' => true]);
    }

    public function answer(Request $request, int $id): JsonResponse
    {
        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $validator = Validator::make($request->all(), [
            'visibility' => 'required|string|in:private,public',
            'answer' => 'required|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation failed: ' . $validator->errors()->first()], 422);
        }

        $q = InstructorQuestion::findOrFail($id);

        $visibility = (string) $request->input('visibility');
        $answer = (string) $request->input('answer');

        $q->status = $visibility === 'public' ? 'answered_public' : 'answered_private';
        $q->answered_by = (int) $admin->id;
        $q->answered_at = now();
        $q->answer_visibility = $visibility;
        $q->answer_text = $answer;
        $q->save();

        // Deliver the answer via chat logs.
        if ($visibility === 'public') {
            // Broadcast: inst-only message (students can see instructor broadcasts).
            $chat = new ChatLog();
            $chat->course_date_id = (int) $q->course_date_id;
            $chat->inst_id = (int) $admin->id;
            $chat->student_id = null;
            $chat->body = $answer;
            $chat->save();
        } else {
            // Private: must be tied to the student_id to preserve privacy.
            $chat = new ChatLog();
            $chat->course_date_id = (int) $q->course_date_id;
            $chat->inst_id = (int) $admin->id;
            $chat->student_id = (int) $q->student_id;
            $chat->body = $answer;
            $chat->save();
        }

        return response()->json(['success' => true]);
    }

    public function sendToAi(Request $request, int $id): JsonResponse
    {
        $admin = auth('admin')->user();
        if (!$admin) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $q = InstructorQuestion::findOrFail($id);

        // Minimal v1 "RAG": search approved docs for keyword hits.
        // If we can't cite sources, refuse.
        $cleanQuestion = trim(preg_replace('/\s+/', ' ', (string) $q->question));
        $sources = $this->findSources($cleanQuestion);

        $decision = 'refused';
        $payload = [
            'answer' => null,
            'sources' => $sources,
            'confidence' => 'low',
            'reason' => null,
        ];

        if (count($sources) === 0) {
            $q->ai_status = 'refused';
            $q->ai_generated_at = now();
            $q->ai_confidence = 'low';
            $q->ai_sources = [];
            $q->ai_answer_instructor = 'Needs instructor judgment (no citable source found in approved materials).';
            $q->ai_answer_student = 'I can\'t answer that from the approved course materials. Please ask your instructor.';
            $q->save();

            $this->logAi($q->id, (int) $admin->id, $this->buildPrompt($cleanQuestion, $sources), $sources, [
                'refused' => true,
                'reason' => 'no_sources',
            ], $decision);

            return response()->json(['success' => true]);
        }

        // Provide a deterministic, citation-backed suggestion without requiring Llama yet.
        $decision = 'answered';
        $confidence = count($sources) >= 3 ? 'high' : 'med';
        $instructorAnswer = "Instructor-ready: Based on the course materials, address the question using the cited section(s) below.";
        $studentAnswer = "Student-friendly: Please review the cited section(s) below; your instructor will clarify.";

        $q->ai_status = 'ready';
        $q->ai_generated_at = now();
        $q->ai_confidence = $confidence;
        $q->ai_sources = $sources;
        $q->ai_answer_instructor = $instructorAnswer;
        $q->ai_answer_student = $studentAnswer;
        $q->save();

        $this->logAi($q->id, (int) $admin->id, $this->buildPrompt($cleanQuestion, $sources), $sources, [
            'refused' => false,
            'instructor_ready' => $instructorAnswer,
            'student_friendly' => $studentAnswer,
            'confidence' => $confidence,
        ], $decision);

        return response()->json(['success' => true]);
    }

    private function buildPrompt(string $cleanQuestion, array $sources): string
    {
        $srcText = json_encode($sources);
        return "Rewrite the student question clearly, then answer ONLY using the provided sources. Include citations.\n\nQuestion: {$cleanQuestion}\n\nSources(JSON): {$srcText}";
    }

    private function logAi(int $instructorQuestionId, int $requestedBy, string $prompt, array $sources, array $response, string $decision): void
    {
        AiChatLog::create([
            'instructor_question_id' => $instructorQuestionId,
            'requested_by' => $requestedBy,
            'prompt' => $prompt,
            'sources' => $sources,
            'response' => $response,
            'decision' => $decision,
        ]);
    }

    private function findSources(string $question): array
    {
        $roots = [
            base_path('docs'),
            public_path('docs'),
        ];

        $terms = array_values(array_filter(preg_split('/[^a-zA-Z0-9]+/', strtolower($question))));
        $terms = array_slice(array_unique($terms), 0, 12);
        if (count($terms) === 0) {
            return [];
        }

        $candidates = [];
        foreach ($roots as $root) {
            if (!is_dir($root)) {
                continue;
            }

            $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($root));
            foreach ($it as $file) {
                if (!$file->isFile()) {
                    continue;
                }

                $path = (string) $file->getPathname();
                if (!preg_match('/\.(md|txt|html?)$/i', $path)) {
                    continue;
                }

                $content = @file_get_contents($path);
                if (!is_string($content) || $content === '') {
                    continue;
                }

                $hay = strtolower($content);
                $score = 0;
                foreach ($terms as $t) {
                    if ($t === '' || strlen($t) < 4) {
                        continue;
                    }
                    if (str_contains($hay, $t)) {
                        $score++;
                    }
                }

                if ($score >= 2) {
                    $rel = str_replace(base_path() . DIRECTORY_SEPARATOR, '', $path);
                    $candidates[] = [
                        'doc' => str_replace('\\', '/', $rel),
                        'score' => $score,
                    ];
                }
            }
        }

        usort($candidates, fn($a, $b) => ($b['score'] <=> $a['score']));
        $candidates = array_slice($candidates, 0, 5);

        return array_map(function ($c) {
            return [
                'doc' => $c['doc'],
                'section' => null,
                'score' => $c['score'],
            ];
        }, $candidates);
    }
}
