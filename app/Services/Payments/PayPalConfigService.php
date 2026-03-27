<?php

namespace App\Services\Payments;

class PayPalConfigService
{
    public function mode(): string
    {
        $mode = trim((string) (setting('payments.paypal.mode') ?? ''));

        if ($mode === '') {
            $mode = trim((string) config('services.paypal.mode', ''));
        }

        return in_array($mode, ['sandbox', 'live'], true) ? $mode : 'sandbox';
    }

    public function clientId(): ?string
    {
        $clientId = trim((string) (setting('payments.paypal.client_id') ?? ''));

        if ($clientId === '') {
            $clientId = trim((string) config('services.paypal.client_id', ''));
        }

        return $clientId !== '' ? $clientId : null;
    }

    public function clientSecret(): ?string
    {
        $clientSecret = trim((string) (setting('payments.paypal.client_secret') ?? ''));

        if ($clientSecret === '') {
            $clientSecret = trim((string) config('services.paypal.client_secret', ''));
        }

        return $clientSecret !== '' ? $clientSecret : null;
    }

    public function isConfigured(): bool
    {
        return !empty($this->clientId()) && !empty($this->clientSecret());
    }
}
