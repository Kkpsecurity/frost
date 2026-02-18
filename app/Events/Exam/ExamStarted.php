<?php

namespace App\Events\Exam;

use App\Models\ExamAuth;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ExamStarted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $examAuth;

    public function __construct(ExamAuth $examAuth)
    {
        $this->examAuth = $examAuth;
    }
}
