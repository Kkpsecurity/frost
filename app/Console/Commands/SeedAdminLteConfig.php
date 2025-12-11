<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Database\Seeders\AdminLteConfigSeeder;

class SeedAdminLteConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'adminlte:seed-config {--force : Force overwrite existing settings}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed AdminLTE configuration settings from default config file to database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force')) {
            // Check if AdminLTE settings already exist
            $existingSettings = DB::table('settings')->where('key', 'like', 'adminlte.%')->count();

            if ($existingSettings > 0) {
                $this->warn("AdminLTE settings already exist in the database ({$existingSettings} settings found).");
                $this->info("Use --force option to overwrite existing settings.");

                if (!$this->confirm('Do you want to proceed and overwrite existing settings?')) {
                    $this->info('Operation cancelled.');
                    return;
                }
            }
        }

        $this->info('Seeding AdminLTE configuration...');

        $seeder = new AdminLteConfigSeeder();
        $seeder->setCommand($this);
        $seeder->run();

        $this->newLine();
        $this->info('✨ AdminLTE configuration seeded successfully!');
        $this->info('🔗 Visit: http://frost.test/admin/admin-center/settings/adminlte/config');
    }
}
