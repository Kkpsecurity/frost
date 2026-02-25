<?php

namespace App\Events\Profile;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SuspiciousLoginDetected
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param  User    $user       The user who logged in.
     * @param  string  $ipAddress  The IP address of the current login.
     * @param  string  $knownIp    The last known trusted IP.
     * @param  string  $userAgent  The browser / client user-agent string.
     */
    public function __construct(
        public readonly User $user,
        public readonly string $ipAddress,
        public readonly string $knownIp,
        public readonly string $userAgent = '',
    ) {}
}
