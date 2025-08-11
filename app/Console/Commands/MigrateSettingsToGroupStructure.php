<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateSettingsToGroupStructure extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'settings:migrate-to-groups
                            {--dry-run : Run the migration without making changes}
                            {--force : Force the migration even if there are conflicts}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate settings from dot notation (chat.username) to group structure';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $force = $this->option('force');

        $this->info('Starting settings migration to group structure...');

        if ($isDryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
        }

        // Get all settings that use dot notation
        $dotNotationSettings = DB::table('settings')
            ->where('key', 'like', '%.%')
            ->get();

        if ($dotNotationSettings->isEmpty()) {
            $this->info('No dot notation settings found to migrate.');
            return Command::SUCCESS;
        }

        $this->info("Found {$dotNotationSettings->count()} settings with dot notation to migrate:");

        $migratedCount = 0;
        $skippedCount = 0;
        $errorCount = 0;

        foreach ($dotNotationSettings as $setting) {
            // Split the key by the first dot only
            $parts = explode('.', $setting->key, 2);

            if (count($parts) !== 2) {
                $this->warn("Skipping {$setting->key} - invalid dot notation format");
                $skippedCount++;
                continue;
            }

            $group = $parts[0];
            $newKey = $parts[1];

            // Check if a setting with the new key already exists
            $existingSetting = DB::table('settings')
                ->where('key', $newKey)
                ->where('group', $group)
                ->first();

            if ($existingSetting && !$force) {
                $this->warn("Skipping {$setting->key} - conflicts with existing setting {$group}.{$newKey}");
                $skippedCount++;
                continue;
            }

            $this->line("Migrating: {$setting->key} → group: '{$group}', key: '{$newKey}'");

            if (!$isDryRun) {
                try {
                    // Start transaction for this setting
                    DB::beginTransaction();

                    if ($existingSetting && $force) {
                        // Delete existing setting
                        DB::table('settings')
                            ->where('id', $existingSetting->id)
                            ->delete();
                        $this->warn("  Deleted existing conflicting setting");
                    }

                    // Update the setting with new structure
                    DB::table('settings')
                        ->where('id', $setting->id)
                        ->update([
                            'group' => $group,
                            'key' => $newKey,
                            'updated_at' => now()
                        ]);

                    DB::commit();
                    $migratedCount++;
                    $this->info("  ✓ Migrated successfully");

                } catch (\Exception $e) {
                    DB::rollBack();
                    $this->error("  ✗ Error migrating {$setting->key}: " . $e->getMessage());
                    $errorCount++;
                }
            } else {
                $migratedCount++;
            }
        }

        $this->newLine();
        $this->info('Migration Summary:');
        $this->line("Migrated: {$migratedCount}");
        $this->line("Skipped: {$skippedCount}");

        if ($errorCount > 0) {
            $this->line("Errors: {$errorCount}");
        }

        if ($isDryRun) {
            $this->warn('This was a dry run. Use --force to actually perform the migration.');
            $this->info('Run: php artisan settings:migrate-to-groups');
        } else {
            $this->success('Settings migration completed!');
        }

        return Command::SUCCESS;
    }
}
