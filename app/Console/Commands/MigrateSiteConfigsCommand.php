<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Services\SiteConfigService;

class MigrateSiteConfigsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:site-configs {--verify : Only verify the migration without running it}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate site_configs data to settings table';

    /**
     * Execute the console command.
     */
    public function handle(SiteConfigService $siteConfigService): int
    {
        if ($this->option('verify')) {
            return $this->verifyMigration($siteConfigService);
        }

        $this->info('Starting site_configs to settings migration...');

        try {
            // Check if site_configs table exists and has data
            if (!$this->checkSiteConfigsTable()) {
                return Command::FAILURE;
            }

            // Run the migration
            $this->info('Running migration...');
            $exitCode = $this->call('migrate', ['--path' => 'database/migrations/2025_08_07_120000_migrate_site_configs_to_settings.php']);

            if ($exitCode !== 0) {
                $this->error('Migration failed!');
                return Command::FAILURE;
            }

            $this->info('Migration completed successfully!');

            // Verify the migration
            $this->verifyMigration($siteConfigService);

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Migration failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Check if site_configs table exists and has the expected data
     */
    private function checkSiteConfigsTable(): bool
    {
        try {
            $count = DB::table('site_configs')->count();
            $this->info("Found {$count} records in site_configs table");

            if ($count === 0) {
                $this->warn('site_configs table is empty. The migration will create the test data.');
            }

            return true;
        } catch (\Exception $e) {
            $this->error('site_configs table does not exist or is not accessible: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Verify the migration was successful
     */
    private function verifyMigration(SiteConfigService $siteConfigService): int
    {
        $this->info('Verifying migration...');

        try {
            // Check settings table
            $settingsCount = DB::table('settings')->where('key', 'like', 'site.%')
                ->orWhere('key', 'like', 'class.%')
                ->orWhere('key', 'like', 'student.%')
                ->orWhere('key', 'like', 'instructor.%')
                ->orWhere('key', 'like', 'chat.%')
                ->count();

            $this->info("Found {$settingsCount} migrated settings in settings table");

            // Display migrated settings
            $this->table(
                ['Category', 'Key', 'Value'],
                $this->formatSettingsForTable($siteConfigService->getAllSettings())
            );

            // Check specific values
            $this->info('Verifying specific values:');
            $this->line('Company Name: ' . $siteConfigService->getSiteSettings()['company_name']);
            $this->line('Support Email: ' . $siteConfigService->getSiteSettings()['support_email']);
            $this->line('Class Start Soon Seconds: ' . $siteConfigService->getClassSettings()['starts_soon_seconds']);

            $this->info('✅ Migration verification completed!');
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Verification failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Format settings for table display
     */
    private function formatSettingsForTable(array $settings): array
    {
        $rows = [];

        foreach ($settings as $category => $categorySettings) {
            foreach ($categorySettings as $key => $value) {
                $displayValue = is_string($value) && strlen($value) > 50
                    ? substr($value, 0, 50) . '...'
                    : $value;

                $rows[] = [
                    ucfirst($category),
                    $key,
                    $displayValue
                ];
            }
        }

        return $rows;
    }
}
