<?php

namespace App\Events\Classroom;

use App\Models\ChatLog;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired when an instructor sends a chat message to the classroom.
 * Listeners fan-out individual browser/database notifications to each enrolled student.
 */
class InstructorMessageSent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly ChatLog $chatLog,
        public readonly int $courseDateId,
    ) {}
}
