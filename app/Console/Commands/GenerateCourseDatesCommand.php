<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Services\Frost\Scheduling\CourseDateGeneratorService;

class GenerateCourseDatesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'course:generate-dates
                            {--days=5 : Number of days ahead to generate}
                            {--date= : Specific date to generate (YYYY-MM-DD)}
                            {--range= : Date range to generate (YYYY-MM-DD,YYYY-MM-DD)}
                            {--preview : Preview what would be generated without creating}
                            {--cleanup : Clean up old CourseDate records}
                            {--cleanup-days=30 : Days old to consider for cleanup}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate CourseDate records for scheduled courses';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🗓️  CourseDate Generator Starting...');

        $service = new CourseDateGeneratorService();

        // Handle cleanup option
        if ($this->option('cleanup')) {
            $cleanupDays = (int) $this->option('cleanup-days');
            $this->info("🧹 Cleaning up CourseDate records older than {$cleanupDays} days...");

            $cleanedCount = $service->cleanupOldCourseDates($cleanupDays);
            $this->info("✅ Cleaned up {$cleanedCount} old CourseDate records");

            if (!$this->hasGenerationOptions()) {
                return 0;
            }
            $this->newLine();
        }

        // Determine date range
        [$startDate, $endDate] = $this->getDateRange();

        $this->info("📅 Date Range: {$startDate->format('Y-m-d')} to {$endDate->format('Y-m-d')}");
        $this->info("📊 Total Days: " . ($startDate->diffInDays($endDate) + 1));

        // Handle preview option
        if ($this->option('preview')) {
            $this->showPreview($service, $startDate, $endDate);
            return 0;
        }

        // Confirm generation
        if (!$this->confirm('Do you want to proceed with CourseDate generation?', true)) {
            $this->info('❌ Generation cancelled');
            return 1;
        }

        // Generate CourseDate records
        $this->newLine();
        $this->info('🚀 Generating CourseDate records...');

        $results = $service->generateCourseDatesForRange($startDate, $endDate);

        // Display results
        $this->displayResults($results);

        return 0;
    }

    /**
     * Get the date range for generation
     */
    private function getDateRange(): array
    {
        // Handle specific date
        if ($this->option('date')) {
            $date = Carbon::parse($this->option('date'));
            return [$date, $date];
        }

        // Handle date range
        if ($this->option('range')) {
            $range = explode(',', $this->option('range'));
            if (count($range) !== 2) {
                $this->error('Range format should be: YYYY-MM-DD,YYYY-MM-DD');
                exit(1);
            }

            $startDate = Carbon::parse(trim($range[0]));
            $endDate = Carbon::parse(trim($range[1]));

            if ($startDate > $endDate) {
                $this->error('Start date must be before or equal to end date');
                exit(1);
            }

            return [$startDate, $endDate];
        }

        // Default: generate for specified number of days ahead
        $days = (int) $this->option('days');
        $startDate = now()->addDay(); // Start from tomorrow
        $endDate = now()->addDays($days);

        return [$startDate, $endDate];
    }

    /**
     * Show preview of what would be generated
     */
    private function showPreview(CourseDateGeneratorService $service, Carbon $startDate, Carbon $endDate): void
    {
        $this->info('🔍 Preview Mode - No CourseDate records will be created');
        $this->newLine();

        $preview = $service->previewGeneration($startDate, $endDate);

        // Show period summary
        $this->table(
            ['Period', 'Value'],
            [
                ['Date Range', $preview['period']['start'] . ' to ' . $preview['period']['end']],
                ['Total Days', $preview['period']['total_days']],
                ['Weekdays', $preview['period']['weekdays']],
                ['Estimated Total CourseDate Records', $preview['estimated_total']]
            ]
        );

        // Show course breakdown
        if (!empty($preview['courses'])) {
            $this->newLine();
            $this->info('📚 Courses to Process:');

            $courseRows = [];
            foreach ($preview['courses'] as $course) {
                $courseRows[] = [
                    $course['id'],
                    $course['title'],
                    $course['units_count'],
                    $course['estimated_dates']
                ];
            }

            $this->table(
                ['Course ID', 'Course Title', 'Units', 'Est. Dates'],
                $courseRows
            );
        } else {
            $this->warn('⚠️ No active courses found with CourseUnits');
        }
    }

    /**
     * Display generation results
     */
    private function displayResults(array $results): void
    {
        $this->newLine();

        // Summary table
        $this->table(
            ['Metric', 'Value'],
            [
                ['Date Range', $results['period']['start'] . ' to ' . $results['period']['end']],
                ['Total Days', $results['period']['total_days']],
                ['Courses Processed', $results['courses_processed']],
                ['CourseDate Records Created', $results['dates_created']],
                ['Existing Records Skipped', $results['dates_skipped']],
                ['Total Errors', count($results['errors'])]
            ]
        );

        // Show success message
        if ($results['dates_created'] > 0) {
            $this->info("✅ Successfully created {$results['dates_created']} CourseDate records!");
        }

        if ($results['dates_skipped'] > 0) {
            $this->comment("ℹ️  Skipped {$results['dates_skipped']} existing CourseDate records");
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

    /**
     * Check if any generation options are provided
     */
    private function hasGenerationOptions(): bool
    {
        return $this->option('date') ||
               $this->option('range') ||
               $this->option('days') ||
               $this->option('preview');
    }
}
