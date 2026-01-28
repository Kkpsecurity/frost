<?php

namespace App\Listeners\Sentinel;

use App\Services\Sentinel\SentinelBridgeService;
use Illuminate\Support\Facades\Log;

abstract class BaseSentinelListener
{
    protected SentinelBridgeService $sentinel;

    public function __construct(SentinelBridgeService $sentinel)
    {
        $this->sentinel = $sentinel;
    }

    /**
     * Capture event
     *
     * @param string $eventType
     * @param array $eventData
     * @param string $severity
     * @return void
     */
    protected function captureEvent(string $eventType, array $eventData, string $severity = 'info'): void
    {
        try {
            $this->sentinel->captureEvent($eventType, $eventData, $severity);
        } catch (\Exception $e) {
            Log::error("Sentinel listener failed to capture event", [
                'event_type' => $eventType,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle failed job
     */
    public function failed($event, $exception): void
    {
        Log::error(static::class . " failed", [
            'event' => $event,
            'exception' => $exception->getMessage(),
        ]);
    }
}
