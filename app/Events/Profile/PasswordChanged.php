<?php

namespace App\Events\Profile;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PasswordChanged
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public readonly User $user,
        public readonly string $ipAddress = '',
        public readonly string $userAgent = '',
    ) {}
}
