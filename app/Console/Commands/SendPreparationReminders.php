<?php

namespace App\Console\Commands;

use App\Models\CourseAuth;
use App\Models\CourseDate;
use App\Models\User;
use App\Notifications\Preparation\ClassApproachingNotification;
use App\Notifications\Preparation\ClassStartingSoonNotification;
use App\Notifications\Preparation\ClassTomorrowNotification;
use Illuminate\Console\Command;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Log;

/**
 * Sends time-based pre-classroom preparation reminders to enrolled students:
 *
 *   - 3 days before class:  "Class Approaching"    (user-controllable, high priority)
 *   - 1 day before class:   "Class Tomorrow"       (always sent, high priority)
 *   - ~1 hour before class: "Class Starting Soon"  (always sent, critical)
 *
 * Deduplication: checks the `notifications` table for an existing record per
 * notifiable (user) + notification type + course_date_id. Safe to run frequently.
 *
 * Run every 15 minutes via Kernel.php so the 1-hour window fires reliably:
 *   $schedule->command('preparation:send-reminders')->everyFifteenMinutes()
 *
 * Manual usage:
 *   php artisan preparation:send-reminders
 *   php artisan preparation:send-reminders --dry-run
 */
class SendPreparationReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'preparation:send-reminders
                            {--dry-run : Display what would be sent without actually sending}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send pre-classroom preparation reminders (3-day, 1-day, 1-hour)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $isDryRun = (bool) $this->option('dry-run');

        $this->info('Sending preparation reminders...');

        if ($isDryRun) {
            $this->warn('DRY RUN MODE — No notifications will be sent');
        }

        $total = 0;
        $total += $this->sendApproachingReminders($isDryRun);
        $total += $this->sendTomorrowReminders($isDryRun);
        $total += $this->sendStartingSoonReminders($isDryRun);

        if ($total === 0) {
            $this->info('No preparation reminders needed at this time.');
        } else {
            $this->info("Total reminders sent: {$total}");
        }

        Log::info('SendPreparationReminders completed', [
            'total'   => $total,
            'dry_run' => $isDryRun,
        ]);

        return Command::SUCCESS;
    }

    // -------------------------------------------------------------------------
    // Reminder methods
    // -------------------------------------------------------------------------

    /**
     * "Class Approaching" — starts_at is exactly 3 calendar days from today.
     */
    protected function sendApproachingReminders(bool $isDryRun): int
    {
        $targetDate  = now()->addDays(3)->toDateString();
        $courseDates = CourseDate::with(['CourseUnit'])
            ->whereDate('starts_at', $targetDate)
            ->whereHas('CourseUnit')
            ->get();

        $count = 0;

        foreach ($courseDates as $courseDate) {
            $count += $this->notifyStudentsForCourseDate(
                $courseDate,
                ClassApproachingNotification::class,
                fn(CourseAuth $ca) => new ClassApproachingNotification($ca, $courseDate, 3),
                $isDryRun,
                'class_approaching (3 days)',
            );
        }

        return $count;
    }

    /**
     * "Class Tomorrow" — starts_at is tomorrow (1 calendar day from today).
     */
    protected function sendTomorrowReminders(bool $isDryRun): int
    {
        $targetDate  = now()->addDay()->toDateString();
        $courseDates = CourseDate::with(['CourseUnit'])
            ->whereDate('starts_at', $targetDate)
            ->whereHas('CourseUnit')
            ->get();

        $count = 0;

        foreach ($courseDates as $courseDate) {
            $count += $this->notifyStudentsForCourseDate(
                $courseDate,
                ClassTomorrowNotification::class,
                fn(CourseAuth $ca) => new ClassTomorrowNotification($ca, $courseDate),
                $isDryRun,
                'class_tomorrow',
            );
        }

        return $count;
    }

    /**
     * "Class Starting Soon" — starts_at is within a 50–70 minute window from now.
     *
     * Using a 20-minute window (50–70 min) to ensure the notification fires even
     * if the cron runs slightly early or late. Runs every 15 minutes, so each
     * class will be caught in at most one run (deduplication blocks retries).
     */
    protected function sendStartingSoonReminders(bool $isDryRun): int
    {
        $windowStart = now()->addMinutes(50);
        $windowEnd   = now()->addMinutes(70);

        $courseDates = CourseDate::with(['CourseUnit'])
            ->whereBetween('starts_at', [$windowStart, $windowEnd])
            ->whereHas('CourseUnit')
            ->get();

        $count = 0;

        foreach ($courseDates as $courseDate) {
            $count += $this->notifyStudentsForCourseDate(
                $courseDate,
                ClassStartingSoonNotification::class,
                fn(CourseAuth $ca) => new ClassStartingSoonNotification($ca, $courseDate),
                $isDryRun,
                'class_starting_soon (~1 hr)',
            );
        }

        return $count;
    }

    // -------------------------------------------------------------------------
    // Shared helpers
    // -------------------------------------------------------------------------

    /**
     * Locate all enrolled students for a CourseDate and dispatch the notification.
     *
     * - Resolves the course from CourseDate → CourseUnit → course_id
     * - Finds all active CourseAuth records for that course
     * - Deduplicates via the notifications table (type + course_auth_id + course_date_id)
     *
     * @param  \Closure(CourseAuth): \Illuminate\Notifications\Notification  $notificationFactory
     */
    protected function notifyStudentsForCourseDate(
        CourseDate $courseDate,
        string $notificationClass,
        \Closure $notificationFactory,
        bool $isDryRun,
        string $label,
    ): int {
        $courseId = $courseDate->CourseUnit?->course_id;

        if (! $courseId) {
            return 0;
        }

        $courseAuths = CourseAuth::with(['User', 'Course'])
            ->where('course_id', $courseId)
            ->whereNull('completed_at')
            ->whereNull('disabled_at')
            ->get();

        $count = 0;

        foreach ($courseAuths as $courseAuth) {
            $user = $courseAuth->User;

            if (! $user) {
                continue;
            }

            // Deduplicate: one notification per student per class date per type.
            // Cast to jsonb explicitly — the notifications.data column is stored as text in this project.
            $alreadySent = DatabaseNotification::where('notifiable_type', User::class)
                ->where('notifiable_id', $user->id)
                ->where('type', $notificationClass)
                ->whereRaw("(data::jsonb)->>'course_auth_id' = ?", [(string) $courseAuth->id])
                ->whereRaw("(data::jsonb)->>'course_date_id' = ?", [(string) $courseDate->id])
                ->exists();

            if ($alreadySent) {
                continue;
            }

            if ($isDryRun) {
                $this->line(
                    "Would send [{$label}] to {$user->email} "
                        . "(course_auth_id={$courseAuth->id}, course_date_id={$courseDate->id})"
                );
            } else {
                $user->notify($notificationFactory($courseAuth));

                Log::info("SendPreparationReminders: sent {$label}", [
                    'user_id'        => $user->id,
                    'course_auth_id' => $courseAuth->id,
                    'course_date_id' => $courseDate->id,
                ]);
            }

            $count++;
        }

        return $count;
    }
}
