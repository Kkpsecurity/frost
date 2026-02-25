<?php

namespace App\Events\System;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PolicyUpdated
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param  string  $policyName  Human-readable name of the updated policy (e.g. "Privacy Policy").
     * @param  string  $summary     Brief description of what changed.
     * @param  string  $url         URL to view the full updated policy (optional).
     */
    public function __construct(
        public readonly string $policyName,
        public readonly string $summary = '',
        public readonly string $url = '',
    ) {}
}
