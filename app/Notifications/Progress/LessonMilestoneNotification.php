<?php

namespace App\Notifications\Progress;

use App\Models\StudentUnit;
use App\Notifications\Concerns\UsesUserNotificationPreferences;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

/**
 * Sent when a student reaches a lesson completion milestone (25%, 50%, or 75%).
 */
class LessonMilestoneNotification extends Notification implements ShouldQueue
{
    use Queueable;
    use UsesUserNotificationPreferences;

    public function __construct(
        protected readonly StudentUnit $studentUnit,
        protected readonly int $milestone,
        protected readonly int $completedLessons,
        protected readonly int $totalLessons,
    ) {}

    public function via(object $notifiable): array
    {
        $key = 'milestone_' . $this->milestone;

        return $this->preferredChannels(
            $notifiable,
            'progress.' . $key,
            config('user_notifications.notifications.progress.' . $key . '.channels', ['database', 'browser']),
            (bool) config('user_notifications.notifications.progress.' . $key . '.user_controllable', true),
        );
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'              => 'progress.milestone_' . $this->milestone,
            'title'             => $this->milestone . '% Complete!',
            'message'           => 'Great progress! You have completed ' . $this->completedLessons . ' of ' . $this->totalLessons . ' lessons.',
            'milestone'         => $this->milestone,
            'completed_lessons' => $this->completedLessons,
            'total_lessons'     => $this->totalLessons,
            'course_auth_id'    => $this->studentUnit->course_auth_id,
            'student_unit_id'   => $this->studentUnit->id,
            'url'               => '/classroom',
            'icon'              => 'trending-up',
            'priority'          => 'low',
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }

    /**
     * Prevent duplicate milestone notifications for same StudentUnit + milestone.
     */
    public function shouldSend(object $notifiable, string $channel): bool
    {
        if ($channel !== 'database') {
            return true;
        }

        return ! $notifiable->notifications()
            ->whereRaw("(data::jsonb)->>'type' = ?", ['progress.milestone_' . $this->milestone])
            ->whereRaw("(data::jsonb)->>'student_unit_id' = ?", [(string) $this->studentUnit->id])
            ->exists();
    }
}
