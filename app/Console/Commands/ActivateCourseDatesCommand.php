<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Services\Frost\Scheduling\CourseDateActivationService;

class ActivateCourseDatesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'course:activate-dates
                            {--date= : Specific date to activate (YYYY-MM-DD)}
                            {--preview : Preview what would be activated without making changes}
                            {--timezone=America/New_York : Timezone for date calculations}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Activate CourseDate records for today or a specific date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 CourseDate Activation Starting...');

        $service = new CourseDateActivationService();
        $timezone = $this->option('timezone');

        // Handle preview option
        if ($this->option('preview')) {
            if ($this->option('date')) {
                $this->error('Preview option is only available for today. Use without --date.');
                return 1;
            }

            $this->showPreview($service, $timezone);
            return 0;
        }

        // Handle specific date option
        if ($this->option('date')) {
            $date = Carbon::parse($this->option('date'));
            $this->info("📅 Activating CourseDate records for: {$date->format('Y-m-d l')}");

            $results = $service->activateCourseDatesForDate($date);
        } else {
            // Default: activate for today
            $today = Carbon::now($timezone)->format('Y-m-d l');
            $this->info("📅 Activating CourseDate records for today: {$today}");

            $results = $service->activateCourseDatesForToday($timezone);
        }

        // Display results
        $this->displayResults($results);

        return 0;
    }

    /**
     * Show preview of what would be activated
     */
    private function showPreview(CourseDateActivationService $service, string $timezone): void
    {
        $this->info('🔍 Preview Mode - No CourseDate records will be activated');
        $this->newLine();

        $preview = $service->previewActivationForToday($timezone);

        $today = Carbon::now($timezone)->format('Y-m-d l');
        $this->info("📅 Date: {$today} ({$preview['timezone']})");
        $this->info("📊 Inactive CourseDate records found: {$preview['inactive_count']}");

        if ($preview['inactive_count'] > 0) {
            $this->newLine();
            $this->info('📚 CourseDate Records to Activate:');

            $tableRows = [];
            foreach ($preview['courses'] as $course) {
                $tableRows[] = [
                    $course['id'],
                    $course['course'],
                    $course['unit'],
                    $course['start_time'],
                ];
            }

            $this->table(
                ['ID', 'Course', 'Unit', 'Start Time'],
                $tableRows
            );
        } else {
            $this->comment('ℹ️  No inactive CourseDate records found for today');
        }
    }

    /**
     * Display activation results
     */
    private function displayResults(array $results): void
    {
        $this->newLine();

        // Summary table
        $this->table(
            ['Metric', 'Value'],
            [
                ['Date', $results['date']],
                ['Inactive Records Found', $results['found_inactive']],
                ['Successfully Activated', $results['activated']],
                ['Errors', count($results['errors'])]
            ]
        );

        // Show success message
        if ($results['activated'] > 0) {
            $this->info("✅ Successfully activated {$results['activated']} CourseDate records!");

            $this->newLine();
            $this->info('📚 Activated CourseDate Records:');

            $detailRows = [];
            foreach ($results['details'] as $detail) {
                $detailRows[] = [
                    $detail['id'],
                    $detail['course'],
                    $detail['unit'],
                    $detail['start_time']
                ];
            }

            $this->table(
                ['ID', 'Course', 'Unit', 'Start Time'],
                $detailRows
            );
        } else {
            $this->comment('ℹ️  No CourseDate records were activated');
        }

        // Show errors if any
        if (!empty($results['errors'])) {
            $this->newLine();
            $this->error('❌ Errors encountered:');
            foreach ($results['errors'] as $error) {
                $this->line("   • {$error}");
            }
        }
    }
}
