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

        // Activate CourseDate records daily at 06:00 AM ET (before classroom creation)
        $schedule->command('course:activate-dates')
            ->dailyAt('06:00')
            ->timezone('America/New_York')
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/course-date-activation.log'));

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

        // Close classroom sessions after 12 AM the day after class date
        // Runs every hour after midnight to catch sessions that should be closed
        $schedule->command('classrooms:close-sessions')
            ->hourly()
            ->between('00:00', '06:00') // Only run between midnight and 6 AM
            ->timezone('America/New_York')
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/classroom-session-closure.log'));
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
