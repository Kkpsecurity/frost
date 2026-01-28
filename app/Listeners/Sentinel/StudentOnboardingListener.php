<?php

namespace App\Listeners\Sentinel;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;

class StudentOnboardingListener extends BaseSentinelListener
{
    /**
     * Handle student registration
     */
    public function handleRegistered(Registered $event): void
    {
        $this->captureEvent('student.registered', [
            'user_id' => $event->user->id,
            'email' => $event->user->email,
            'name' => $event->user->name,
            'role' => $event->user->role ?? 'student',
        ], 'info');
    }

    /**
     * Handle student login
     */
    public function handleLogin(Login $event): void
    {
        if ($event->user && $event->user->role === 'student') {
            $this->captureEvent('student.login', [
                'user_id' => $event->user->id,
                'email' => $event->user->email,
                'ip_address' => request()->ip(),
            ], 'info');
        }
    }

    /**
     * Handle student logout
     */
    public function handleLogout(Logout $event): void
    {
        if ($event->user && $event->user->role === 'student') {
            $this->captureEvent('student.logout', [
                'user_id' => $event->user->id,
                'session_duration' => $this->calculateSessionDuration($event->user),
            ], 'info');
        }
    }

    /**
     * Calculate session duration
     */
    protected function calculateSessionDuration($user): int
    {
        // Simplified - you can track actual session times
        return 0;
    }

    /**
     * Subscribe to events
     */
    public function subscribe($events): array
    {
        return [
            Registered::class => 'handleRegistered',
            Login::class => 'handleLogin',
            Logout::class => 'handleLogout',
        ];
    }
}
