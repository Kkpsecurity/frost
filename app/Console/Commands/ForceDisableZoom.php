<?php

namespace App\Console\Commands;

use App\Models\ZoomCreds;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Force disable ALL Zoom credentials - runs every minute
 *
 * This is a nuclear option to ensure Zoom stays disabled.
 * Scheduled to run every minute to catch and disable any
 * credentials that might have been enabled through any means.
 */
class ForceDisableZoom extends Command
{
    protected $signature = 'zoom:force-disable
                            {--silent : Run silently without output}';

    protected $description = 'Force disable ALL Zoom credentials (scheduled every minute)';

    public function handle()
    {
        $silent = $this->option('silent');

        try {
            // Get count of enabled credentials before
            $enabledCount = DB::table('zoom_creds')
                ->where('zoom_status', 'enabled')
                ->count();

            if ($enabledCount > 0) {
                // Force update ALL zoom_creds to disabled using raw query to bypass model hooks
                DB::table('zoom_creds')->update(['zoom_status' => 'disabled']);

                Log::warning('ForceDisableZoom: Disabled Zoom credentials', [
                    'count' => $enabledCount,
                    'timestamp' => now()->toIso8601String(),
                ]);

                if (!$silent) {
                    $this->warn("⚠️  Force-disabled {$enabledCount} Zoom credential(s)");
                }
            } else {
                if (!$silent) {
                    $this->info('✓ All Zoom credentials are already disabled');
                }
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            Log::error('ForceDisableZoom: Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if (!$silent) {
                $this->error('✗ Error: ' . $e->getMessage());
            }

            return Command::FAILURE;
        }
    }
}
