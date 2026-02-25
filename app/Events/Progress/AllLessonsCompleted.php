<?php

namespace App\Events\Progress;

use App\Models\StudentUnit;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired when a student completes all lessons in their course unit.
 * Typically fires just before the exam becomes available.
 */
class AllLessonsCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly StudentUnit $studentUnit,
    ) {}
}
