<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CodexSecureController extends Controller
{
    /**
     * Display secure services dashboard
     */
    public function index()
    {
        $metrics = $this->getSecurityMetrics();

        return view('admin.codex.services.secure', compact('metrics'));
    }

    /**
     * Refresh security metrics
     */
    public function refresh(Request $request): JsonResponse
    {
        try {
            // Clear cached metrics
            Cache::forget('security_metrics');

            // Regenerate metrics
            $metrics = $this->getSecurityMetrics();

            Log::info('Security metrics refreshed', [
                'admin_id' => auth('admin')->id(),
                'timestamp' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Security metrics refreshed successfully',
                'metrics' => $metrics,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to refresh security metrics', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to refresh metrics',
            ], 500);
        }
    }

    /**
     * Get security metrics
     */
    private function getSecurityMetrics(): array
    {
        return Cache::remember('security_metrics', 300, function () {
            return [
                'failed_logins' => $this->getFailedLoginCount(),
                'active_sessions' => $this->getActiveSessionCount(),
                'recent_admin_activity' => $this->getRecentAdminActivity(),
                'system_alerts' => $this->getSystemAlerts(),
                'database_size' => $this->getDatabaseSize(),
                'last_backup' => $this->getLastBackupTime(),
            ];
        });
    }

    /**
     * Get failed login count (last 24 hours)
     */
    private function getFailedLoginCount(): int
    {
        // Implement based on your logging system
        return 0;
    }

    /**
     * Get active session count
     */
    private function getActiveSessionCount(): int
    {
        try {
            return DB::table('sessions')
                ->where('last_activity', '>', Carbon::now()->subMinutes(30)->timestamp)
                ->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get recent admin activity
     */
    private function getRecentAdminActivity(): array
    {
        try {
            $activities = DB::table('sessions')
                ->join('admins', 'sessions.user_id', '=', 'admins.id')
                ->select('admins.email', 'sessions.last_activity', 'sessions.ip_address')
                ->where('sessions.last_activity', '>', Carbon::now()->subHours(24)->timestamp)
                ->orderBy('sessions.last_activity', 'desc')
                ->limit(10)
                ->get();

            return $activities->map(function ($activity) {
                return [
                    'email' => $activity->email,
                    'time' => Carbon::createFromTimestamp($activity->last_activity)->diffForHumans(),
                    'ip' => $activity->ip_address,
                ];
            })->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get system alerts
     */
    private function getSystemAlerts(): array
    {
        $alerts = [];

        // Check disk space
        $diskFree = disk_free_space('/');
        $diskTotal = disk_total_space('/');
        $diskPercent = ($diskFree / $diskTotal) * 100;

        if ($diskPercent < 10) {
            $alerts[] = [
                'level' => 'critical',
                'message' => 'Low disk space: ' . round($diskPercent, 2) . '% remaining',
            ];
        }

        return $alerts;
    }

    /**
     * Get database size
     */
    private function getDatabaseSize(): string
    {
        try {
            $size = DB::selectOne("SELECT pg_database_size(current_database()) as size");
            $sizeInMB = round($size->size / 1024 / 1024, 2);

            return $sizeInMB . ' MB';
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }

    /**
     * Get last backup time
     */
    private function getLastBackupTime(): string
    {
        // Implement based on your backup system
        return 'Not configured';
    }
}
