<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();

        // Reset all Zoom accounts to disabled at 5:00 AM daily (before class starts)
        $schedule->command('zoom:disable-all --force')
            ->dailyAt('05:00')
            ->timezone('America/New_York')
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/zoom-daily-reset.log'));

        // Ensure today's (or next class day) CourseDate exists before other classroom automation.
        // NOTE: With --simulate-weekend-monday, weekends can still have a "today" class date for testing.
        $schedule->command('course:ensure-today-date')
            ->dailyAt('05:30')
            ->timezone('America/New_York')
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/course-date-ensure-today.log'));

        // Auto-create classrooms daily at 07:00 AM ET
        $schedule->command('classrooms:auto-create-today')
            ->dailyAt('07:00')
            ->timezone('America/New_York')
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/classroom-auto-create.log'));

        // Generate CourseDate records weekly on Sunday at 10:00 PM ET
        // Generates dates for the upcoming 5 days (Mon-Fri) with cleanup
        $schedule->command('course:generate-dates --days=5 --cleanup --cleanup-days=30')
            ->weeklyOn(0, '22:00') // Sunday at 10:00 PM
            ->timezone('America/New_York')
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/course-date-generation.log'));

        // Generate NEXT MONTH's CourseDate records 3 days before month end at 11:00 PM ET
        // Ensures next month's schedule is ready in advance (e.g., Oct 28 generates Nov)
        $schedule->command('course:generate-monthly --months=1 --cleanup --cleanup-days=60')
            ->monthlyOn(28, '23:00') // 28th day of month at 11:00 PM (3 days before end)
            ->timezone('America/New_York')
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/course-date-monthly-generation.log'));

        // Close classroom sessions at midnight - Official end of class day
        // Even if class ends at 6 PM, schedule officially ends at 12:00 AM
        $schedule->command('classrooms:close-sessions')
            ->dailyAt('00:00') // Daily at midnight
            ->timezone('America/New_York')
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/classroom-session-closure.log'));

        // Timeout abandoned offline sessions every 5 minutes
        // Marks sessions as failed, creates StudentLesson with dnc_at, deducts credits
        $schedule->command('sessions:timeout-abandoned --minutes=30')
            ->everyFiveMinutes()
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/session-timeout.log'));

        // Expire pending participation challenges every minute.
        // Ensures challenges fail on-time even if the student leaves the site/tab.
        $schedule->command('challenges:expire-pending')
            ->everyMinute()
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/challenges-expire-pending.log'));

        // Monitor cron job health every 15 minutes
        $schedule->command('cron:health-check --silent')
            ->cron('*/15 * * * *') // Every 15 minutes
            ->timezone('America/New_York')
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/cron-health-check.log'));

        // Startup check - runs at boot time and periodically to ensure system health
        $schedule->command('cron:startup-check --silent')
            ->cron('*/30 * * * *') // Every 30 minutes
            ->timezone('America/New_York')
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/cron-startup-check.log'));

        // Send exam reminders daily at 9:00 AM ET
        // Reminds students about ready but not-started exams at configurable intervals (3, 7, 14 days)
        $schedule->command('exams:send-reminders')
            ->dailyAt('09:00')
            ->timezone('America/New_York')
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/exam-reminders.log'));

        // Send pre-classroom preparation reminders every 15 minutes.
        // Covers: class_approaching (3 days), class_tomorrow (1 day), class_starting_soon (~1 hr).
        // Runs frequently so the 1-hour window fires reliably regardless of class start time.
        // Deduplication in each notification prevents duplicate sends.
        $schedule->command('preparation:send-reminders')
            ->everyFifteenMinutes()
            ->timezone('America/New_York')
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/preparation-reminders.log'));

        // Send course expiry reminders daily at 08:00 AM ET.
        // Fires CourseExpiringSoon events for enrollments expiring in exactly 30 or 7 days.
        // Deduplication handled inside each notification's shouldSend() method.
        $schedule->command('progress:send-expiry-reminders')
            ->dailyAt('08:00')
            ->timezone('America/New_York')
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/course-expiry-reminders.log'));
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
