<?php

namespace App\Console\Commands;

use App\Models\ZoomCreds;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Disable all Zoom credentials
 *
 * This command sets all zoom_creds.zoom_status to 'disabled'
 * Useful for emergency shutdown or manual cleanup
 */
class DisableAllZoomCreds extends Command
{
    protected $signature = 'zoom:disable-all
                            {--force : Skip confirmation prompt}';

    protected $description = 'Disable all Zoom credentials (set zoom_status to disabled)';

    public function handle()
    {
        $force = $this->option('force');

        // Get count of enabled credentials
        $enabledCount = ZoomCreds::where('zoom_status', 'enabled')->count();
        $totalCount = ZoomCreds::count();

        if ($enabledCount === 0) {
            $this->info('✓ All Zoom credentials are already disabled');
            return Command::SUCCESS;
        }

        $this->warn("Found {$enabledCount} enabled Zoom credential(s) out of {$totalCount} total");

        // Show which credentials will be disabled
        $enabledCreds = ZoomCreds::where('zoom_status', 'enabled')->get();
        $this->newLine();
        $this->line('Credentials to be disabled:');
        foreach ($enabledCreds as $cred) {
            $this->line("  - ID: {$cred->id} | Email: {$cred->zoom_email}");
        }
        $this->newLine();

        // Confirm action
        if (!$force) {
            if (!$this->confirm('Are you sure you want to disable all Zoom credentials?', false)) {
                $this->info('Operation cancelled');
                return Command::SUCCESS;
            }
        }

        try {
            DB::beginTransaction();

            // Disable all credentials
            $updated = DB::table('zoom_creds')
                ->where('zoom_status', 'enabled')
                ->update(['zoom_status' => 'disabled']);

            DB::commit();

            $this->newLine();
            $this->info("✓ Successfully disabled {$updated} Zoom credential(s)");

            // Show final status
            $this->newLine();
            $this->line('Final status:');
            $allCreds = ZoomCreds::all();
            foreach ($allCreds as $cred) {
                $status = $cred->zoom_status === 'disabled' ? '✓' : '✗';
                $this->line("  {$status} ID: {$cred->id} | Email: {$cred->zoom_email} | Status: {$cred->zoom_status}");
            }

            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('✗ Error disabling Zoom credentials: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
