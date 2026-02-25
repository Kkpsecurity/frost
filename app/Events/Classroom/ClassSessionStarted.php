<?php

namespace App\Events\Classroom;

use App\Models\InstUnit;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired when an instructor starts a classroom session (creates InstUnit).
 * Listeners should fan-out to all students enrolled in the course date.
 */
class ClassSessionStarted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly InstUnit $instUnit,
        public readonly int $courseDateId,
    ) {}
}
