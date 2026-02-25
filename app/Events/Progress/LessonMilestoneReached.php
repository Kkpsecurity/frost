<?php

namespace App\Events\Progress;

use App\Models\StudentUnit;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired when a student hits a lesson-completion milestone (25%, 50%, or 75%).
 */
class LessonMilestoneReached
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly StudentUnit $studentUnit,
        public readonly int $milestone,       // 25, 50, or 75
        public readonly int $completedLessons,
        public readonly int $totalLessons,
    ) {}
}
