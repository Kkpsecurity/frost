<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\CourseDate;
use App\Models\User;
use Illuminate\Support\Facades\Log;

/**
 * ClassroomDataArrayService (lightweight placeholder)
 *
 * This service provides a minimal data structure for classroom data array
 * and poll data used by instructor/assistant dashboards. It can be expanded
 * later to include full classroom context.
 */
class ClassroomDataArrayService
{
    protected ?CourseDate $courseDate;
    protected ?User $user;

    public function __construct(?CourseDate $courseDate = null, ?User $user = null)
    {
        $this->courseDate = $courseDate;
        $this->user = $user;
    }

    /**
     * Build full classroom data array (placeholder)
     */
    public function buildClassroomDataArray(string $template = 'full_classroom_data'): array
    {
        return $this->basePayload();
    }

    /**
     * Build lightweight polling payload (placeholder)
     */
    public function buildClassroomPollData(): array
    {
        $payload = $this->basePayload();

        // Add active lesson state if InstUnit exists
        $courseDate = $this->courseDate;
        $instUnit = $courseDate?->InstUnit;
        $activeLesson = null;
        if ($instUnit) {
            $activeInstLesson = \App\Models\InstLesson::where('inst_unit_id', $instUnit->id)
                ->whereNull('completed_at')
                ->orderBy('id', 'asc')
                ->first();
            if ($activeInstLesson) {
                $activeLesson = [
                    'id' => $activeInstLesson->id,
                    'lesson_id' => $activeInstLesson->lesson_id,
                    'is_paused' => $activeInstLesson->is_paused,
                    'completed_at' => $activeInstLesson->completed_at,
                ];
            }
        }
        $payload['activeLesson'] = $activeLesson;
        return $payload;
    }

    /**
     * Base payload shared by data and poll responses
     */
    private function basePayload(): array
    {
        $courseDate = $this->courseDate;

        return [
            'courseDate' => $courseDate,
            'courseDates' => $courseDate ? [$courseDate] : [],
            'course' => $courseDate?->course,
            'instUnit' => null,
            'instLessons' => [],
            'lessons' => [],
            'students' => [],
            'chat' => [
                'messages' => [],
            ],
        ];
    }
}
