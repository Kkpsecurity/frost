<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\InstUnit;
use App\Models\CourseDate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CloseClassroomSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'classrooms:close-sessions {--dry-run : Show what would be closed without actually closing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Close classroom sessions after 12 AM the day after class date (only if instructor is present)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $now = Carbon::now();
        
        $this->info("Starting classroom session closure check at {$now->format('Y-m-d H:i:s')}");
        if ($isDryRun) {
            $this->warn('DRY RUN MODE - No sessions will actually be closed');
        }

        // Find InstUnits that should be closed
        // Logic: Close sessions after 12 AM the day after the class date, but only if instructor is present
        $sessionsToClose = $this->findSessionsToClose();

        if ($sessionsToClose->isEmpty()) {
            $this->info('No classroom sessions need to be closed at this time.');
            return 0;
        }

        $this->info("Found {$sessionsToClose->count()} classroom sessions to close:");

        $closedCount = 0;
        $errors = 0;

        foreach ($sessionsToClose as $instUnit) {
            try {
                $courseDate = $instUnit->CourseDate;
                $classDayEnd = Carbon::parse($courseDate->starts_at)->addDay()->startOfDay(); // 12:00 AM next day
                
                $this->line("Processing InstUnit {$instUnit->id}:");
                $this->line("  - Course Date: {$courseDate->id}");
                $this->line("  - Class Date: " . Carbon::parse($courseDate->starts_at)->format('Y-m-d'));
                $this->line("  - Should close after: {$classDayEnd->format('Y-m-d H:i:s')}");
                $this->line("  - Instructor ID: {$instUnit->created_by}");
                $this->line("  - Current time: {$now->format('Y-m-d H:i:s')}");

                if (!$isDryRun) {
                    $instUnit->update([
                        'completed_at' => $now,
                        'completed_by' => $instUnit->created_by // Same instructor who started it
                    ]);

                    Log::info('Classroom session closed automatically', [
                        'inst_unit_id' => $instUnit->id,
                        'course_date_id' => $courseDate->id,
                        'instructor_id' => $instUnit->created_by,
                        'closed_at' => $now,
                        'class_date' => Carbon::parse($courseDate->starts_at)->format('Y-m-d')
                    ]);

                    $this->info("  ✅ Session closed successfully");
                } else {
                    $this->info("  🔍 Would close this session");
                }

                $closedCount++;

            } catch (\Exception $e) {
                $this->error("  ❌ Error closing InstUnit {$instUnit->id}: " . $e->getMessage());
                
                Log::error('Failed to close classroom session', [
                    'inst_unit_id' => $instUnit->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                $errors++;
            }
        }

        $this->info("\n" . str_repeat('=', 50));
        $this->info("Classroom session closure summary:");
        $this->info("Sessions processed: {$sessionsToClose->count()}");
        if (!$isDryRun) {
            $this->info("Sessions closed: {$closedCount}");
            $this->info("Errors: {$errors}");
        } else {
            $this->info("Sessions that would be closed: {$closedCount}");
        }

        return $errors > 0 ? 1 : 0;
    }

    /**
     * Find InstUnit sessions that should be closed
     * 
     * Criteria:
     * - InstUnit has created_by (instructor is present)
     * - InstUnit does not have completed_at (not already closed)
     * - Current time is after 12 AM the day after the class date
     */
    private function findSessionsToClose()
    {
        $now = Carbon::now();

        return InstUnit::whereNotNull('created_by') // Instructor must be present
            ->whereNull('completed_at') // Not already closed
            ->with('CourseDate') // Eager load CourseDate
            ->get()
            ->filter(function ($instUnit) use ($now) {
                if (!$instUnit->CourseDate) {
                    return false;
                }

                // Calculate when this session should be closed (12 AM the day after class)
                $classDate = Carbon::parse($instUnit->CourseDate->starts_at);
                $closeAfter = $classDate->copy()->addDay()->startOfDay(); // 12:00 AM next day

                // Close if current time is after the close time
                return $now->isAfter($closeAfter);
            });
    }

    /**
     * Get human-readable session info for display
     */
    private function getSessionInfo($instUnit): string
    {
        $courseDate = $instUnit->CourseDate;
        $classDate = Carbon::parse($courseDate->starts_at)->format('Y-m-d');
        $startTime = Carbon::parse($courseDate->starts_at)->format('H:i');
        
        return "InstUnit {$instUnit->id} (Class: {$classDate} {$startTime}, Instructor: {$instUnit->created_by})";
    }
}
