<?php

namespace App\Notifications\Exam;

use App\Models\ExamAuth;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Notifications\Concerns\UsesUserNotificationPreferences;

class ExamPassedNotification extends Notification implements ShouldQueue
{
    use Queueable, UsesUserNotificationPreferences;

    protected ExamAuth $examAuth;

    public function __construct(ExamAuth $examAuth)
    {
        $this->examAuth = $examAuth;
    }

    public function via(object $notifiable): array
    {
        return $this->preferredChannels(
            $notifiable,
            'notification_exam.exam_passed',
            config('user_notifications.notifications.exams.exam_passed.channels', ['database']),
            (bool) config('user_notifications.notifications.exams.exam_passed.user_controllable', true),
        );
    }

    public function toMail(object $notifiable): MailMessage
    {
        $courseName = $this->examAuth->CourseAuth->Course->title ?? 'your course';
        $score = number_format($this->examAuth->score, 1);

        return (new MailMessage)
            ->subject('🎉 Congratulations - You Passed!')
            ->greeting('Excellent Work!')
            ->line('Congratulations! You have successfully passed your exam.')
            ->line('**Course:** ' . $courseName)
            ->line('**Score:** ' . $score . '%')
            ->line('**Required:** ' . $this->examAuth->PassScore . '%')
            ->action('View Results', route('exam.review', $this->examAuth->id))
            ->line('You can now review your exam answers and continue with your certification.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Exam Passed!',
            'message' => 'Congratulations! You passed with a score of ' . number_format($this->examAuth->score, 1) . '%',
            'exam_auth_id' => $this->examAuth->id,
            'course_auth_id' => $this->examAuth->course_auth_id,
            'score' => $this->examAuth->score,
            'pass_score' => $this->examAuth->PassScore,
            'icon' => 'trophy',
            'color' => 'success',
            'priority' => 'high',
            'url' => route('exam.review', $this->examAuth->id),
        ];
    }
}
