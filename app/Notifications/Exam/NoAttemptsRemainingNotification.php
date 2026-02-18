<?php

namespace App\Notifications\Exam;

use App\Models\ExamAuth;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NoAttemptsRemainingNotification extends Notification implements ShouldQueue
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

        return (new MailMessage)
            ->subject('🚫 No Attempts Remaining - ' . $courseName)
            ->greeting('Attempts Exhausted')
            ->line('You have used all available exam attempts for this course.')
            ->line('**Course:** ' . $courseName)
            ->line('**Attempts Remaining:** 0')
            ->line('To continue, please contact support for assistance.')
            ->action('Contact Support', url('/support'))
            ->line('Our team will review your request and provide guidance.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'No Attempts Remaining',
            'message' => 'All exam attempts exhausted. Contact support to proceed.',
            'exam_auth_id' => $this->examAuth->id,
            'course_auth_id' => $this->examAuth->course_auth_id,
            'course_name' => $this->examAuth->CourseAuth->Course->title ?? null,
            'attempts_remaining' => 0,
            'icon' => 'ban',
            'color' => 'danger',
            'priority' => 'critical',
            'url' => url('/support'),
        ];
    }
}
