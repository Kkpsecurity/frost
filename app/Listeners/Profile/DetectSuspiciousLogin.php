<?php

namespace App\Listeners\Profile;

use App\Events\Profile\SuspiciousLoginDetected;
use App\Models\UserPref;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;

/**
 * Detects logins from a previously unseen IP address and fires
 * SuspiciousLoginDetected when the IP differs from the last stored value.
 *
 * Not queued — runs synchronously during the request so the current
 * IP is reliably available from the injected Request object.
 */
class DetectSuspiciousLogin
{
    /**
     * The current HTTP request (injected via the service container).
     */
    public function __construct(protected Request $request) {}

    /**
     * Handle the Login event.
     */
    public function handle(Login $event): void
    {
        $user      = $event->user;
        $currentIp = $this->request->ip() ?? '';
        $userAgent = $this->request->userAgent() ?? '';

        // Retrieve the stored trusted IP from user prefs.
        $pref = UserPref::where('user_id', $user->id)
            ->where('pref_name', 'last_login_ip')
            ->first();

        $knownIp = $pref ? ($pref->pref_value ?? '') : '';

        // If a known IP exists and the current IP differs, fire the event.
        if ($knownIp !== '' && $knownIp !== $currentIp) {
            SuspiciousLoginDetected::dispatch($user, $currentIp, $knownIp, $userAgent);
        }

        // Always update the stored IP so the next login has an up-to-date baseline.
        UserPref::updateOrCreate(
            ['user_id' => $user->id, 'pref_name' => 'last_login_ip'],
            ['pref_value' => $currentIp]
        );
    }
}
