<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Sentinel\SentinelBridgeService;
use App\Models\SentinelEvent;
use App\Models\SentinelHealthCheck;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class SentinelController extends Controller
{
    protected SentinelBridgeService $sentinel;

    public function __construct(SentinelBridgeService $sentinel)
    {
        $this->sentinel = $sentinel;
    }

    /**
     * Manually send an event
     *
     * POST /api/sentinel/event
     */
    public function sendEvent(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'event_type' => 'required|string|max:255',
            'event_data' => 'required|array',
            'severity' => 'nullable|in:info,warning,error,critical',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $this->sentinel->captureEvent(
                $request->input('event_type'),
                $request->input('event_data'),
                $request->input('severity', 'info')
            );

            return response()->json([
                'success' => true,
                'message' => 'Event captured successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to capture event: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Health check endpoint
     *
     * GET /api/sentinel/health
     */
    public function health(): JsonResponse
    {
        $startTime = microtime(true);

        $checks = [];

        // Database check
        try {
            DB::connection()->getPdo();
            $dbTime = (microtime(true) - $startTime) * 1000;
            $checks['database'] = [
                'status' => 'healthy',
                'response_time' => round($dbTime, 2),
            ];
        } catch (\Exception $e) {
            $checks['database'] = [
                'status' => 'down',
                'error' => $e->getMessage(),
            ];
        }

        // Cache check
        try {
            $cacheStart = microtime(true);
            Cache::put('sentinel_health_check', true, 10);
            $cached = Cache::get('sentinel_health_check');
            $cacheTime = (microtime(true) - $cacheStart) * 1000;

            $checks['cache'] = [
                'status' => $cached ? 'healthy' : 'degraded',
                'response_time' => round($cacheTime, 2),
            ];
        } catch (\Exception $e) {
            $checks['cache'] = [
                'status' => 'down',
                'error' => $e->getMessage(),
            ];
        }

        // n8n check
        $n8nCheck = $this->sentinel->testConnection();
        $checks['n8n'] = [
            'status' => $n8nCheck['success'] ? 'healthy' : 'down',
            'url' => $n8nCheck['url'],
            'message' => $n8nCheck['message'] ?? null,
        ];

        // Storage check
        try {
            $storagePath = storage_path('app');
            $freeSpace = disk_free_space($storagePath);
            $totalSpace = disk_total_space($storagePath);
            $usedPercentage = round((($totalSpace - $freeSpace) / $totalSpace) * 100, 2);

            $checks['storage'] = [
                'status' => $usedPercentage < 90 ? 'healthy' : 'degraded',
                'free_space_gb' => round($freeSpace / 1024 / 1024 / 1024, 2),
                'used_percentage' => $usedPercentage,
            ];
        } catch (\Exception $e) {
            $checks['storage'] = [
                'status' => 'unknown',
                'error' => $e->getMessage(),
            ];
        }

        // Overall status
        $overallStatus = 'healthy';
        foreach ($checks as $check) {
            if ($check['status'] === 'down') {
                $overallStatus = 'down';
                break;
            } elseif ($check['status'] === 'degraded' && $overallStatus !== 'down') {
                $overallStatus = 'degraded';
            }
        }

        $totalTime = (microtime(true) - $startTime) * 1000;

        // Store health check result
        SentinelHealthCheck::create([
            'check_type' => 'system',
            'status' => $overallStatus,
            'response_time' => round($totalTime, 2),
            'details' => $checks,
        ]);

        return response()->json([
            'status' => $overallStatus,
            'checks' => $checks,
            'response_time_ms' => round($totalTime, 2),
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Test n8n connection
     *
     * POST /api/sentinel/test
     */
    public function testConnection(): JsonResponse
    {
        $result = $this->sentinel->testConnection();

        return response()->json($result, $result['success'] ? 200 : 500);
    }

    /**
     * Get statistics
     *
     * GET /api/sentinel/stats
     */
    public function stats(): JsonResponse
    {
        $stats = $this->sentinel->getStatistics();

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Get events list
     *
     * GET /api/sentinel/events
     */
    public function events(Request $request): JsonResponse
    {
        $query = SentinelEvent::query();

        // Filters
        if ($request->has('severity')) {
            $query->where('severity', $request->input('severity'));
        }

        if ($request->has('event_type')) {
            $query->where('event_type', 'like', $request->input('event_type') . '%');
        }

        if ($request->has('sent')) {
            $query->where('sent_to_n8n', (bool) $request->input('sent'));
        }

        if ($request->has('hours')) {
            $query->where('created_at', '>=', now()->subHours($request->input('hours')));
        }

        // Pagination
        $perPage = min($request->input('per_page', 15), 100);
        $events = $query->latest()->paginate($perPage);

        return response()->json($events);
    }

    /**
     * Get event details
     *
     * GET /api/sentinel/events/{id}
     */
    public function eventDetails(int $id): JsonResponse
    {
        $event = SentinelEvent::with('user')->find($id);

        if (!$event) {
            return response()->json([
                'success' => false,
                'message' => 'Event not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $event,
        ]);
    }

    /**
     * Get health checks
     *
     * GET /api/sentinel/health-checks
     */
    public function healthChecks(Request $request): JsonResponse
    {
        $query = SentinelHealthCheck::query();

        // Filters
        if ($request->has('check_type')) {
            $query->where('check_type', $request->input('check_type'));
        }

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->has('minutes')) {
            $query->where('created_at', '>=', now()->subMinutes($request->input('minutes')));
        }

        // Pagination
        $perPage = min($request->input('per_page', 15), 100);
        $checks = $query->latest()->paginate($perPage);

        return response()->json($checks);
    }

    /**
     * Cleanup old events
     *
     * POST /api/sentinel/cleanup
     */
    public function cleanup(): JsonResponse
    {
        try {
            $count = $this->sentinel->cleanup();

            return response()->json([
                'success' => true,
                'message' => "Cleaned up {$count} old events",
                'deleted_count' => $count,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cleanup failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
