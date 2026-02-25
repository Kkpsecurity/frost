<?php

namespace App\Notifications\Exam;

use App\Models\ExamAuth;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FinalAttemptWarningNotification extends Notification implements ShouldQueue
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
            ->subject('⚠️ Final Attempt Warning - ' . $courseName)
            ->greeting('Important Notice')
            ->line('This is your FINAL attempt for this exam.')
            ->line('**Course:** ' . $courseName)
            ->line('**Attempts Remaining:** 1')
            ->action('View Exam', route('classroom.course', $this->examAuth->course_auth_id))
            ->line('Please review all course material thoroughly before attempting.')
            ->line('If you do not pass, you will need to contact support for additional attempts.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Final Attempt Warning',
            'message' => 'This is your FINAL exam attempt. Review all material carefully before proceeding.',
            'exam_auth_id' => $this->examAuth->id,
            'course_auth_id' => $this->examAuth->course_auth_id,
            'course_name' => $this->examAuth->CourseAuth->Course->title ?? null,
            'attempts_remaining' => 1,
            'icon' => 'exclamation-circle',
            'color' => 'danger',
            'priority' => 'critical',
            'url' => route('classroom.course', $this->examAuth->course_auth_id),
        ];
    }
}
