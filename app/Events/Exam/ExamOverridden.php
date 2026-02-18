<?php

namespace App\Events\Exam;

use App\Models\ExamAuth;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ExamOverridden
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $examAuth;
    public $action;

    public function __construct(ExamAuth $examAuth, string $action = 'reset')
    {
        $this->examAuth = $examAuth;
        $this->action = $action;
    }
}
