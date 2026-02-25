<?php

namespace App\Events\Classroom;

use App\Models\StudentUnit;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired when an instructor ejects (kicks) a student from the classroom.
 * The notification is sent only to the affected student.
 */
class StudentEjectedFromClassroom
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly StudentUnit $studentUnit,
        public readonly string $reason,
    ) {}
}
