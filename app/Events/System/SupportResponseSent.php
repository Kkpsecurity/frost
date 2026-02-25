<?php

namespace App\Events\System;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SupportResponseSent
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param  User    $student      The student receiving the reply.
     * @param  string  $subject      The subject / title of the support thread.
     * @param  string  $preview      A short preview of the response body (≤ 160 chars).
     * @param  string  $ticketUrl    URL to view the full thread (optional; defaults to account page).
     */
    public function __construct(
        public readonly User $student,
        public readonly string $subject,
        public readonly string $preview = '',
        public readonly string $ticketUrl = '',
    ) {}
}
