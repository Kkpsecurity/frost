<?php

namespace App\Events\Classroom;

use App\Models\InstLesson;
use App\Models\InstUnit;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired when an instructor starts a lesson within the active classroom session.
 */
class LessonStarted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly InstLesson $instLesson,
        public readonly InstUnit $instUnit,
    ) {}
}
