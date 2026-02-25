<?php

namespace App\Events\System;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MaintenanceScheduled
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param  string  $scheduledAt  Human-readable date/time string (e.g. "Saturday 10 PM – 2 AM ET").
     * @param  string  $message      Optional extra context / reason for the maintenance window.
     */
    public function __construct(
        public readonly string $scheduledAt,
        public readonly string $message = '',
    ) {}
}
