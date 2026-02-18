<?php

namespace App\Notifications\Exam;

use App\Models\ExamAuth;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RetakeAvailableNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $examAuth;

    public function __construct(ExamAuth $examAuth)
    {
        $this->examAuth = $examAuth;
    }

    public function via(object $notifiable): array
    {
        $channels = ['database'];

        $emailEnabled = $notifiable->UserPrefs()
            ->where('key', 'notification_exam.retake_available')
            ->where('value', true)
            ->exists();

        if ($emailEnabled) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $courseName = $this->examAuth->CourseAuth->Course->title ?? 'your course';
        $attemptsRemaining = $this->examAuth->AttemptsRemaining();

        return (new MailMessage)
            ->subject('🔄 Retake Available - ' . $courseName)
            ->greeting('Ready to Retry!')
            ->line('The cooldown period has ended and you can now retake your exam.')
            ->line('**Course:** ' . $courseName)
            ->line('**Attempts Remaining:** ' . $attemptsRemaining)
            ->action('Start Retake', route('classroom', ['course_auth_id' => $this->examAuth->course_auth_id]))
            ->line('Review the material before attempting again. Good luck!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Exam Retake Available',
            'message' => 'Cooldown period ended. You can now retake the exam. ' . $this->examAuth->AttemptsRemaining() . ' attempts remaining.',
            'exam_auth_id' => $this->examAuth->id,
            'course_auth_id' => $this->examAuth->course_auth_id,
            'course_name' => $this->examAuth->CourseAuth->Course->title ?? null,
            'attempts_remaining' => $this->examAuth->AttemptsRemaining(),
            'icon' => 'redo',
            'color' => 'info',
            'priority' => 'medium',
            'url' => route('classroom', ['course_auth_id' => $this->examAuth->course_auth_id]),
        ];
    }
}
