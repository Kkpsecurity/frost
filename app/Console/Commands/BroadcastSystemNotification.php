<?php

namespace App\Console\Commands;

use App\Events\System\MaintenanceScheduled;
use App\Events\System\PolicyUpdated;
use App\Models\User;
use Illuminate\Console\Command;

/**
 * Broadcast a system notification (maintenance or policy update) to all active students.
 *
 * Usage examples:
 *   php artisan system:broadcast --type=maintenance --scheduled-at="Saturday 10 PM – 2 AM ET"
 *   php artisan system:broadcast --type=maintenance --scheduled-at="Sunday 00:00 – 04:00 ET" --message="Database optimisation"
 *   php artisan system:broadcast --type=policy --policy-name="Privacy Policy" --summary="Added GDPR section" --policy-url="https://example.com/privacy"
 *   php artisan system:broadcast --type=maintenance --scheduled-at="..." --dry-run
 */
class BroadcastSystemNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:broadcast
                            {--type=maintenance : Notification type: maintenance | policy}
                            {--scheduled-at= : (maintenance) Human-readable date/time window}
                            {--message= : (maintenance) Optional extra context}
                            {--policy-name= : (policy) Name of the updated policy}
                            {--summary= : (policy) Brief description of changes}
                            {--policy-url= : (policy) URL to the updated policy}
                            {--dry-run : Preview recipient count without firing events}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Broadcast a system notification to all active students (maintenance or policy update)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $type    = $this->option('type');
        $dryRun  = (bool) $this->option('dry-run');

        if (! in_array($type, ['maintenance', 'policy'])) {
            $this->error('--type must be "maintenance" or "policy".');
            return self::FAILURE;
        }

        // Count target students for preview.
        $count = User::where('role_id', 5)->where('is_active', true)->count();

        if ($dryRun) {
            $this->info("[DRY RUN] Would send '{$type}' notification to {$count} active student(s).");
            return self::SUCCESS;
        }

        if ($type === 'maintenance') {
            $scheduledAt = $this->option('scheduled-at');

            if (! $scheduledAt) {
                $scheduledAt = $this->ask('Enter the maintenance window date/time (e.g. "Saturday 10 PM – 2 AM ET")');
            }

            if (! $scheduledAt) {
                $this->error('--scheduled-at is required for maintenance notifications.');
                return self::FAILURE;
            }

            $message = $this->option('message') ?? '';

            $this->info("Firing MaintenanceScheduled event → {$count} student(s)...");
            MaintenanceScheduled::dispatch($scheduledAt, $message);
            $this->info('Done. Notifications queued.');

            return self::SUCCESS;
        }

        // type === 'policy'
        $policyName = $this->option('policy-name');

        if (! $policyName) {
            $policyName = $this->ask('Enter the policy name (e.g. "Privacy Policy")');
        }

        if (! $policyName) {
            $this->error('--policy-name is required for policy notifications.');
            return self::FAILURE;
        }

        $summary   = $this->option('summary') ?? '';
        $policyUrl = $this->option('policy-url') ?? '';

        $this->info("Firing PolicyUpdated event → {$count} student(s)...");
        PolicyUpdated::dispatch($policyName, $summary, $policyUrl);
        $this->info('Done. Notifications queued.');

        return self::SUCCESS;
    }
}
