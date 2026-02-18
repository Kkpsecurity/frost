<?php

namespace App\Events\Exam;

use App\Models\ExamAuth;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ExamTimeWarning
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $examAuth;
    public $minutesRemaining;

    public function __construct(ExamAuth $examAuth, int $minutesRemaining)
    {
        $this->examAuth = $examAuth;
        $this->minutesRemaining = $minutesRemaining;
    }
}
