<?php

namespace App\Listeners\Sentinel;

use Illuminate\Database\Events\QueryExecuted;

class DatabaseQueryListener extends BaseSentinelListener
{
    /**
     * Handle query executed event
     */
    public function handle(QueryExecuted $event): void
    {
        // Only track slow queries
        $slowThreshold = config('sentinel.thresholds.slow_query', 1000);

        if ($event->time >= $slowThreshold) {
            $this->captureEvent('database.slow_query', [
                'sql' => $event->sql,
                'bindings' => $event->bindings,
                'time' => $event->time,
                'connection' => $event->connectionName,
            ], 'warning');
        }
    }
}
