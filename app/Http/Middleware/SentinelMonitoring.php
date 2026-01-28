<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\Sentinel\SentinelBridgeService;

class SentinelMonitoring
{
    protected SentinelBridgeService $sentinel;

    public function __construct(SentinelBridgeService $sentinel)
    {
        $this->sentinel = $sentinel;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!config('sentinel.tracking.api', false)) {
            return $next($request);
        }

        $startTime = microtime(true);

        $response = $next($request);

        $duration = (microtime(true) - $startTime) * 1000;

        // Only track API routes
        if ($request->is('api/*') || $request->is('classroom/*')) {
            $this->trackRequest($request, $response, $duration);
        }

        return $response;
    }

    /**
     * Track the request
     */
    protected function trackRequest(Request $request, Response $response, float $duration): void
    {
        $slowThreshold = config('sentinel.thresholds.slow_request', 3000);

        // Determine severity
        $severity = 'info';
        if ($response->getStatusCode() >= 500) {
            $severity = 'error';
        } elseif ($response->getStatusCode() >= 400) {
            $severity = 'warning';
        } elseif ($duration >= $slowThreshold) {
            $severity = 'warning';
        }

        // Only capture errors and slow requests by default
        if ($severity !== 'info' || config('sentinel.debug', false)) {
            $this->sentinel->captureEvent('api.request', [
                'method' => $request->method(),
                'path' => $request->path(),
                'status' => $response->getStatusCode(),
                'duration_ms' => round($duration, 2),
                'user_id' => auth()->id(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ], $severity);
        }
    }
}
