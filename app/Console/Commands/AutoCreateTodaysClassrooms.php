<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ClassroomAutoCreateService;

/**
 * Artisan command to auto-create classrooms from today's course dates
 * 
 * Usage:
 *   php artisan classrooms:auto-create-today
 *   php artisan classrooms:auto-create-today --dry-run
 *   php artisan classrooms:auto-create-today --only=123
 *   php artisan classrooms:auto-create-today --force --allow-recreate
 */
class AutoCreateTodaysClassrooms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'classrooms:auto-create-today 
                            {--dry-run : Show what would be created without making changes}
                            {--only= : Process only the specified course_date_id}
                            {--force : Force recreation of existing classrooms}
                            {--allow-recreate : Allow recreation when used with --force (requires admin confirmation)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto-create classrooms from today\'s scheduled course dates (runs daily at 07:00 AM ET)';

    private ClassroomAutoCreateService $service;

    public function __construct(ClassroomAutoCreateService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $onlyCourseDateId = $this->option('only');
        $force = $this->option('force');
        $allowRecreate = $this->option('allow-recreate');

        // Handle force/recreate options
        if ($force && !$allowRecreate) {
            $this->error('The --force option requires --allow-recreate flag for safety.');
            return Command::FAILURE;
        }

        if ($force && $allowRecreate) {
            if (!$this->confirmForceRecreate()) {
                $this->info('Operation cancelled.');
                return Command::SUCCESS;
            }
            
            // Handle force recreation logic here if needed
            $this->warn('Force recreation is not yet implemented in this version.');
            return Command::FAILURE;
        }

        // Show configuration
        $this->displayConfiguration($dryRun, $onlyCourseDateId);

        // Execute the service
        $this->info('Starting classroom auto-creation...');
        $startTime = microtime(true);

        try {
            $results = $this->service->createTodaysClassrooms($dryRun, $onlyCourseDateId);
            
            $endTime = microtime(true);
            $duration = round(($endTime - $startTime) * 1000, 2);

            // Display results
            $this->displayResults($results, $duration);

            return $results['success'] ? Command::SUCCESS : Command::FAILURE;

        } catch (\Exception $e) {
            $this->error("Fatal error during classroom creation: {$e->getMessage()}");
            $this->error("Stack trace: {$e->getTraceAsString()}");
            return Command::FAILURE;
        }
    }

    /**
     * Display current configuration
     */
    private function displayConfiguration(bool $dryRun, ?string $onlyCourseDateId): void
    {
        $config = config('auto_classroom', []);
        $timezone = $config['timezone'] ?? 'America/New_York';
        $enabled = $config['enabled'] ?? false;

        $this->info('=== Classroom Auto-Creation Configuration ===');
        $this->line("Enabled: " . ($enabled ? '✓ Yes' : '✗ No'));
        $this->line("Timezone: {$timezone}");
        $this->line("Current time (ET): " . now($timezone)->format('Y-m-d H:i:s T'));
        
        if ($dryRun) {
            $this->warn("DRY RUN MODE: No changes will be made");
        }
        
        if ($onlyCourseDateId) {
            $this->line("Processing only CourseDate ID: {$onlyCourseDateId}");
        }
        
        $this->line('');
    }

    /**
     * Display results summary
     */
    private function displayResults(array $results, float $durationMs): void
    {
        $stats = $results['stats'];
        $errors = $results['errors'];
        $success = $results['success'];

        $this->info('=== Classroom Auto-Creation Results ===');
        
        // Status
        if ($success) {
            $this->info('✓ Operation completed successfully');
        } else {
            $this->error('✗ Operation completed with errors');
        }

        // Statistics
        $this->table(
            ['Metric', 'Count'],
            [
                ['Created', $stats['created']],
                ['Skipped', $stats['skipped']],
                ['Failed', $stats['failed']],
                ['Total Processed', $stats['total_processed']],
                ['Duration (ms)', number_format($durationMs, 2)],
            ]
        );

        // Message
        if (!empty($stats['message'])) {
            $this->line("Message: {$stats['message']}");
        }

        // Errors
        if (!empty($errors)) {
            $this->error("\nErrors encountered:");
            foreach ($errors as $error) {
                $this->error("  • {$error}");
            }
        }

        // Summary message
        if ($stats['created'] > 0) {
            $this->info("\n🎉 Successfully created {$stats['created']} classroom(s)!");
        } elseif ($stats['total_processed'] === 0) {
            $this->info("\n📅 No course dates found for today.");
        } else {
            $this->warn("\n⚠️  No new classrooms were created.");
        }

        // Dry run reminder
        if ($results['dry_run']) {
            $this->warn("\n💡 This was a DRY RUN. No actual changes were made.");
            $this->line("Remove --dry-run to execute the actual creation.");
        }
    }

    /**
     * Confirm force recreation with admin acknowledgment
     */
    private function confirmForceRecreate(): bool
    {
        $this->warn('⚠️  DANGER: Force recreation will delete existing classrooms and recreate them.');
        $this->warn('This action cannot be undone and may disrupt active sessions.');
        $this->line('');

        if (!$this->confirm('Are you absolutely sure you want to proceed?', false)) {
            return false;
        }

        $this->warn('This is your final warning. Existing classroom data will be lost.');
        
        if (!$this->confirm('Type "FORCE RECREATE" to confirm', false)) {
            return false;
        }

        $confirmation = $this->ask('Please type "I UNDERSTAND THE RISKS" to proceed');
        
        return $confirmation === 'I UNDERSTAND THE RISKS';
    }
}
