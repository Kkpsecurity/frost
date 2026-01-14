<?php

namespace App\Console\Commands;

use App\Models\InstUnit;
use App\Models\ZoomCreds;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Close classroom sessions at midnight - Official end of class day
 * This cron runs daily at 12:00 AM (midnight) to close any uncompleted classroom sessions
 * and disable their associated Zoom credentials.
 *
 * Purpose:
 * - Mark InstUnits with completed_at if they're still running past midnight
 * - Disable Zoom credentials to prevent unauthorized access
 * - Clean up any rough courses that didn't end properly
 *
 * Schedule: Daily at 00:00 (midnight) America/New_York timezone
 * Command: php artisan classrooms:close-sessions
 */
class ClassroomCloseSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'classrooms:close-sessions
                            {--dry-run : Run without making any changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Close all active classroom sessions at midnight and disable Zoom credentials';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
        }

        $this->info('Starting classroom session closure at midnight...');

        try {
            DB::beginTransaction();

            // Find all active InstUnits (not completed) from PREVIOUS days only
            // When run at midnight, this closes yesterday's classes
            // When run manually during the day, it only closes past classes, not today's
            $today = Carbon::today()->startOfDay();

            $activeInstUnits = InstUnit::whereNull('completed_at')
                ->whereHas('CourseDate', function ($query) use ($today) {
                    // Only close sessions where the course date's end time is before today
                    $query->where('ends_at', '<', $today);
                })
                ->with(['CourseDate.CourseUnit.Course', 'CreatedBy'])
                ->get();

            $closedCount = 0;
            $zoomCredsDisabled = [];

            foreach ($activeInstUnits as $instUnit) {
                $instructor = $instUnit->CreatedBy;
                $course = $instUnit->CourseDate?->CourseUnit?->Course;
                $courseTitle = strtoupper($course->title ?? '');

                // Determine which Zoom credential was used
                $zoomEmail = $this->getZoomEmail($instructor, $courseTitle);

                if (!$dryRun) {
                    // Mark as completed
                    $instUnit->completed_at = now();
                    $instUnit->completed_by = null; // System closure (null = automated)
                    $instUnit->save();

                    // Disable the Zoom credential
                    $zoomCreds = ZoomCreds::where('zoom_email', $zoomEmail)->first();
                    if ($zoomCreds && $zoomCreds->zoom_status === 'enabled') {
                        $zoomCreds->zoom_status = 'disabled';
                        $zoomCreds->save();
                        $zoomCredsDisabled[] = $zoomEmail;
                    }
                }

                $closedCount++;

                $this->line("Closed InstUnit #{$instUnit->id} - Course: {$courseTitle} - Zoom: {$zoomEmail}");

                Log::info('ClassroomCloseSessions: Closed session at midnight', [
                    'inst_unit_id' => $instUnit->id,
                    'course_date_id' => $instUnit->course_date_id,
                    'instructor_id' => $instUnit->created_by,
                    'closed_at' => now()->toIso8601String(),
                    'zoom_email' => $zoomEmail,
                    'dry_run' => $dryRun
                ]);
            }

            if (!$dryRun) {
                DB::commit();
            } else {
                DB::rollBack();
            }

            $this->newLine();
            $this->info("✓ Closed {$closedCount} classroom session(s)");

            if (count($zoomCredsDisabled) > 0) {
                $this->info('✓ Disabled Zoom credentials: ' . implode(', ', array_unique($zoomCredsDisabled)));
            }

            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();

            $this->error('✗ Error closing classroom sessions: ' . $e->getMessage());
            Log::error('ClassroomCloseSessions: Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return Command::FAILURE;
        }
    }

    /**
     * Determine which Zoom email to use based on instructor and course title
     */
    private function getZoomEmail($instructor, string $courseTitle): string
    {
        // If instructor is admin or sysadmin, use admin Zoom credentials
        if ($instructor && in_array($instructor->role_id, [1, 2])) {
            return 'instructor_admin@stgroupusa.com';
        }

        // Match Class D pattern
        if (strpos($courseTitle, ' D') !== false ||
            strpos($courseTitle, 'D40') !== false ||
            strpos($courseTitle, 'D20') !== false) {
            return 'instructor_d@stgroupusa.com';
        }

        // Match Class G pattern
        if (strpos($courseTitle, ' G') !== false ||
            strpos($courseTitle, 'G40') !== false ||
            strpos($courseTitle, 'G20') !== false) {
            return 'instructor_g@stgroupusa.com';
        }

        // Default to admin instructor
        return 'instructor_admin@stgroupusa.com';
    }
}
