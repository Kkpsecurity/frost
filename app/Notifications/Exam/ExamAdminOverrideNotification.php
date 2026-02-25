<?php

namespace App\Notifications\Exam;

use App\Models\ExamAuth;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Notifications\Concerns\UsesUserNotificationPreferences;

class ExamAdminOverrideNotification extends Notification implements ShouldQueue
{
    use Queueable, UsesUserNotificationPreferences;

    protected ExamAuth $examAuth;
    protected string $action;

    public function __construct(ExamAuth $examAuth, string $action = 'reset')
    {
        $this->examAuth = $examAuth;
        $this->action = $action;
    }

    public function via(object $notifiable): array
    {
        return $this->preferredChannels(
            $notifiable,
            'notification_exam.admin_override',
            config('user_notifications.notifications.exams.admin_override.channels', ['database']),
            (bool) config('user_notifications.notifications.exams.admin_override.user_controllable', true),
        );
    }

    public function toMail(object $notifiable): MailMessage
    {
        $courseName = $this->examAuth->CourseAuth->Course->title ?? 'your course';

        return (new MailMessage)
            ->subject('🔑 Exam Status Updated - ' . $courseName)
            ->greeting('Exam Status Changed')
            ->line('An administrator has updated your exam status.')
            ->line('**Course:** ' . $courseName)
            ->line('**Action:** ' . ucfirst($this->action))
            ->action('View Exam Status', route('classroom.course', $this->examAuth->course_auth_id))
            ->line('Contact support if you have questions about this change.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Exam Status Updated by Admin',
            'message' => 'Administrator ' . $this->action . ' your exam. Check your status.',
            'exam_auth_id' => $this->examAuth->id,
            'course_auth_id' => $this->examAuth->course_auth_id,
            'course_name' => $this->examAuth->CourseAuth->Course->title ?? null,
            'action' => $this->action,
            'icon' => 'user-shield',
            'color' => 'warning',
            'priority' => 'high',
            'url' => route('classroom.course', $this->examAuth->course_auth_id),
        ];
    }
}
