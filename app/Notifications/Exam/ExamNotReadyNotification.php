<?php

namespace App\Notifications\Exam;

use App\Models\ExamAuth;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Notifications\Concerns\UsesUserNotificationPreferences;

class ExamNotReadyNotification extends Notification implements ShouldQueue
{
    use Queueable, UsesUserNotificationPreferences;

    protected ExamAuth $examAuth;
    protected string $reason;

    public function __construct(ExamAuth $examAuth, string $reason = 'requirements not met')
    {
        $this->examAuth = $examAuth;
        $this->reason = $reason;
    }

    public function via(object $notifiable): array
    {
        return $this->preferredChannels(
            $notifiable,
            'notification_exam.exam_not_ready',
            config('user_notifications.notifications.exams.exam_not_ready.channels', ['database']),
            (bool) config('user_notifications.notifications.exams.exam_not_ready.user_controllable', true),
        );
    }

    public function toMail(object $notifiable): MailMessage
    {
        $courseName = $this->examAuth->CourseAuth->Course->title ?? 'your course';

        return (new MailMessage)
            ->subject('⚠️ Exam Not Ready - ' . $courseName)
            ->greeting('Exam Requirements Missing')
            ->line('Your exam cannot be accessed at this time.')
            ->line('**Course:** ' . $courseName)
            ->line('**Reason:** ' . $this->reason)
            ->action('View Course', route('classroom.course', $this->examAuth->course_auth_id))
            ->line('Complete all requirements to unlock your exam.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Exam Not Ready',
            'message' => 'Exam unavailable: ' . $this->reason . '. Complete requirements first.',
            'exam_auth_id' => $this->examAuth->id,
            'course_auth_id' => $this->examAuth->course_auth_id,
            'course_name' => $this->examAuth->CourseAuth->Course->title ?? null,
            'reason' => $this->reason,
            'icon' => 'lock',
            'color' => 'secondary',
            'priority' => 'medium',
            'url' => route('classroom.course', $this->examAuth->course_auth_id),
        ];
    }
}
