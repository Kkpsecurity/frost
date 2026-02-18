<?php

namespace App\Notifications\Exam;

use App\Models\ExamAuth;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExamExpiredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $examAuth;

    public function __construct(ExamAuth $examAuth)
    {
        $this->examAuth = $examAuth;
    }

    public function via(object $notifiable): array
    {
        // CRITICAL: Always send - cannot be disabled by user preference
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $courseName = $this->examAuth->CourseAuth->Course->title ?? 'your course';
        $attemptsRemaining = $this->examAuth->AttemptsRemaining();

        $message = (new MailMessage)
            ->subject('⏱️ Exam Time Expired - ' . $courseName)
            ->greeting('Time Expired')
            ->line('Your exam time has expired before submission.')
            ->line('**Course:** ' . $courseName)
            ->line('**Attempts Remaining:** ' . $attemptsRemaining);

        if ($attemptsRemaining > 0) {
            $message->action('View Retake Options', route('classroom', ['course_auth_id' => $this->examAuth->course_auth_id]))
                ->line('You can retry the exam after the cooldown period.');
        } else {
            $message->line('You have no attempts remaining. Please contact support for assistance.');
        }

        return $message;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Exam Time Expired',
            'message' => 'Your exam time expired before submission. Check your remaining attempts.',
            'exam_auth_id' => $this->examAuth->id,
            'course_auth_id' => $this->examAuth->course_auth_id,
            'course_name' => $this->examAuth->CourseAuth->Course->title ?? null,
            'attempts_remaining' => $this->examAuth->AttemptsRemaining(),
            'icon' => 'exclamation-triangle',
            'color' => 'danger',
            'priority' => 'critical',
            'url' => route('classroom', ['course_auth_id' => $this->examAuth->course_auth_id]),
        ];
    }
}
