<?php

namespace App\Services\Sentinel;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Models\SentinelEvent;
use Exception;

class SentinelBridgeService
{
    protected array $config;
    protected bool $enabled;
    protected bool $debug;
    protected string $n8nUrl;
    protected int $retryAttempts;
    protected int $retryDelay;

    public function __construct()
    {
        $this->config = config('sentinel', []);
        $this->enabled = $this->config['enabled'] ?? false;
        $this->debug = $this->config['debug'] ?? false;
        $this->n8nUrl = $this->config['n8n']['url'] ?? 'http://localhost:5678';
        $this->retryAttempts = $this->config['retry']['attempts'] ?? 3;
        $this->retryDelay = $this->config['retry']['delay'] ?? 5;
    }

    /**
     * Capture and process an event
     *
     * @param string $eventType
     * @param array $eventData
     * @param string $severity
     * @return void
     */
    public function captureEvent(string $eventType, array $eventData, string $severity = 'info'): void
    {
        if (!$this->enabled) {
            return;
        }

        // Check if event should be captured
        if (!$this->shouldCaptureEvent($eventType)) {
            return;
        }

        // Rate limiting
        if ($this->isRateLimited($eventType)) {
            if ($this->debug) {
                Log::warning("Sentinel: Event rate limited", ['type' => $eventType]);
            }
            return;
        }

        try {
            // Sanitize event data
            $sanitizedData = $this->sanitizeData($eventData);

            // Determine severity
            $severity = $this->determineSeverity($eventType, $severity);

            // Create event record
            $event = $this->storeEvent($eventType, $sanitizedData, $severity);

            // Send to n8n (async if queue enabled)
            if ($this->config['queue']['enabled'] ?? true) {
                dispatch(function () use ($event) {
                    $this->sendToN8n($event);
                })->onQueue($this->config['queue']['name'] ?? 'sentinel');
            } else {
                $this->sendToN8n($event);
            }

            if ($this->debug) {
                Log::info("Sentinel: Event captured", [
                    'type' => $eventType,
                    'severity' => $severity,
                    'id' => $event->id
                ]);
            }
        } catch (Exception $e) {
            Log::error("Sentinel: Failed to capture event", [
                'type' => $eventType,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Store event in database
     *
     * @param string $eventType
     * @param array $eventData
     * @param string $severity
     * @return SentinelEvent
     */
    protected function storeEvent(string $eventType, array $eventData, string $severity): SentinelEvent
    {
        return SentinelEvent::create([
            'event_type' => $eventType,
            'event_data' => $eventData,
            'severity' => $severity,
            'sent_to_n8n' => false,
            'user_id' => auth()->id(),
        ]);
    }

    /**
     * Send event to n8n webhook
     *
     * @param SentinelEvent $event
     * @return bool
     */
    protected function sendToN8n(SentinelEvent $event): bool
    {
        $webhookPath = $this->getWebhookPath($event->event_type);
        $url = rtrim($this->n8nUrl, '/') . $webhookPath;

        $payload = [
            'event_id' => $event->id,
            'event_type' => $event->event_type,
            'severity' => $event->severity,
            'timestamp' => $event->created_at->toIso8601String(),
            'data' => $event->event_data,
            'user_id' => $event->user_id,
            'environment' => app()->environment(),
        ];

        $attempt = 0;
        $success = false;

        while ($attempt < $this->retryAttempts && !$success) {
            try {
                $response = Http::timeout($this->config['n8n']['timeout'] ?? 10)
                    ->post($url, $payload);

                if ($response->successful()) {
                    $event->update([
                        'sent_to_n8n' => true,
                        'n8n_response' => $response->json(),
                        'processed_at' => now(),
                    ]);

                    if ($this->debug) {
                        Log::info("Sentinel: Event sent to n8n", [
                            'event_id' => $event->id,
                            'webhook' => $webhookPath,
                            'status' => $response->status()
                        ]);
                    }

                    $success = true;
                } else {
                    throw new Exception("n8n returned status: " . $response->status());
                }
            } catch (Exception $e) {
                $attempt++;

                Log::warning("Sentinel: Failed to send to n8n (attempt {$attempt}/{$this->retryAttempts})", [
                    'event_id' => $event->id,
                    'error' => $e->getMessage(),
                    'webhook' => $webhookPath
                ]);

                if ($attempt < $this->retryAttempts) {
                    $delay = $this->retryDelay * pow($this->config['retry']['multiplier'] ?? 2, $attempt - 1);
                    sleep($delay);
                }
            }
        }

        return $success;
    }

    /**
     * Get webhook path for event type
     *
     * @param string $eventType
     * @return string
     */
    protected function getWebhookPath(string $eventType): string
    {
        $webhooks = $this->config['webhooks'] ?? [];

        // Try to match specific webhook
        foreach ($webhooks as $key => $path) {
            if (Str::startsWith($eventType, $key)) {
                return $path;
            }
        }

        // Fall back to generic webhook
        return $webhooks['generic'] ?? '/webhook/sentinel-event';
    }

    /**
     * Check if event should be captured based on filters
     *
     * @param string $eventType
     * @return bool
     */
    protected function shouldCaptureEvent(string $eventType): bool
    {
        $filters = $this->config['filters'] ?? [];

        // Check exclude patterns first
        foreach ($filters['exclude'] ?? [] as $pattern) {
            if (Str::is($pattern, $eventType)) {
                return false;
            }
        }

        // Check include patterns
        if (!empty($filters['include'])) {
            foreach ($filters['include'] as $pattern) {
                if (Str::is($pattern, $eventType)) {
                    return true;
                }
            }
            return false; // Not in include list
        }

        return true; // No filters, allow all
    }

    /**
     * Check if event type is rate limited
     *
     * @param string $eventType
     * @return bool
     */
    protected function isRateLimited(string $eventType): bool
    {
        if (!($this->config['rate_limit']['enabled'] ?? true)) {
            return false;
        }

        $key = "sentinel:rate_limit:{$eventType}";
        $maxPerMinute = $this->config['rate_limit']['max_per_minute'] ?? 60;

        $count = Cache::get($key, 0);

        if ($count >= $maxPerMinute) {
            return true;
        }

        Cache::put($key, $count + 1, 60); // 1 minute TTL
        return false;
    }

    /**
     * Sanitize event data
     *
     * @param array $data
     * @return array
     */
    protected function sanitizeData(array $data): array
    {
        $removeFields = $this->config['sanitize']['remove_fields'] ?? [];
        $maskFields = $this->config['sanitize']['mask_fields'] ?? [];

        $sanitized = $data;

        // Remove sensitive fields
        foreach ($removeFields as $field) {
            $this->removeField($sanitized, $field);
        }

        // Mask fields
        foreach ($maskFields as $field => $shouldMask) {
            if ($shouldMask) {
                $this->maskField($sanitized, $field);
            }
        }

        return $sanitized;
    }

    /**
     * Recursively remove field from array
     *
     * @param array &$data
     * @param string $field
     * @return void
     */
    protected function removeField(array &$data, string $field): void
    {
        foreach ($data as $key => &$value) {
            if ($key === $field) {
                unset($data[$key]);
            } elseif (is_array($value)) {
                $this->removeField($value, $field);
            }
        }
    }

    /**
     * Recursively mask field in array
     *
     * @param array &$data
     * @param string $field
     * @return void
     */
    protected function maskField(array &$data, string $field): void
    {
        foreach ($data as $key => &$value) {
            if ($key === $field && is_string($value)) {
                if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    // Mask email
                    $parts = explode('@', $value);
                    $data[$key] = substr($parts[0], 0, 2) . '***@' . $parts[1];
                } else {
                    // Mask other strings
                    $data[$key] = substr($value, 0, 3) . str_repeat('*', max(0, strlen($value) - 3));
                }
            } elseif (is_array($value)) {
                $this->maskField($value, $field);
            }
        }
    }

    /**
     * Determine event severity
     *
     * @param string $eventType
     * @param string $default
     * @return string
     */
    protected function determineSeverity(string $eventType, string $default = 'info'): string
    {
        $severityMap = $this->config['severity'] ?? [];

        foreach ($severityMap as $level => $patterns) {
            foreach ($patterns as $pattern) {
                if (Str::is($pattern, $eventType)) {
                    return $level;
                }
            }
        }

        return $default;
    }

    /**
     * Test connection to n8n
     *
     * @return array
     */
    public function testConnection(): array
    {
        try {
            $url = rtrim($this->n8nUrl, '/') . '/healthz';
            $response = Http::timeout(5)->get($url);

            return [
                'success' => $response->successful(),
                'status' => $response->status(),
                'url' => $this->n8nUrl,
                'message' => $response->successful() ? 'Connection successful' : 'Connection failed',
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'url' => $this->n8nUrl,
                'message' => 'Connection failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get statistics
     *
     * @return array
     */
    public function getStatistics(): array
    {
        return [
            'total_events' => SentinelEvent::count(),
            'pending_events' => SentinelEvent::where('sent_to_n8n', false)->count(),
            'sent_events' => SentinelEvent::where('sent_to_n8n', true)->count(),
            'events_by_severity' => SentinelEvent::selectRaw('severity, count(*) as count')
                ->groupBy('severity')
                ->pluck('count', 'severity')
                ->toArray(),
            'recent_events' => SentinelEvent::latest()
                ->take(10)
                ->get(['id', 'event_type', 'severity', 'sent_to_n8n', 'created_at'])
                ->toArray(),
        ];
    }

    /**
     * Clean up old events
     *
     * @return int Number of deleted events
     */
    public function cleanup(): int
    {
        if (!($this->config['storage']['cleanup_enabled'] ?? true)) {
            return 0;
        }

        $retentionDays = $this->config['storage']['retention_days'] ?? 30;
        $cutoffDate = now()->subDays($retentionDays);

        $count = SentinelEvent::where('created_at', '<', $cutoffDate)->delete();

        if ($this->debug && $count > 0) {
            Log::info("Sentinel: Cleaned up old events", ['count' => $count]);
        }

        return $count;
    }
}
