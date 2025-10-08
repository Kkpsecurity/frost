<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Console\Scheduling\Schedule;
use Carbon\Carbon;

/**
 * Cron Manager Controller
 * 
 * Manages and monitors Laravel scheduled tasks (cron jobs)
 * Shows status, execution history, and allows manual triggering
 */
class CronManagerController extends Controller
{
    /**
     * Display the cron manager dashboard
     */
    public function index(): View
    {
        $cronJobs = $this->getCronJobs();
        $recentLogs = $this->getRecentCronLogs();
        
        $content = [
            'title' => 'Cron Manager',
            'subtitle' => 'Laravel Scheduled Tasks Management',
            'cron_jobs' => $cronJobs,
            'recent_logs' => $recentLogs,
            'stats' => [
                'total_jobs' => count($cronJobs),
                'active_jobs' => count(array_filter($cronJobs, fn($job) => $job['is_active'])),
                'recent_executions' => count($recentLogs),
                'last_execution' => $recentLogs ? $recentLogs[0]['executed_at'] : null
            ]
        ];

        return view('admin.cron-manager.index', compact('content'));
    }

    /**
     * Get all scheduled cron jobs from Laravel
     */
    private function getCronJobs(): array
    {
        $jobs = [];
        
        // Define known cron jobs based on our system
        $knownJobs = [
            [
                'name' => 'Course Date Activation',
                'command' => 'course:activate-dates',
                'schedule' => 'Daily at 06:00 AM ET',
                'description' => 'Activates CourseDate records for today',
                'log_file' => 'course-date-activation.log',
                'is_active' => true,
                'category' => 'Course Management'
            ],
            [
                'name' => 'Auto Create Classrooms',
                'command' => 'classrooms:auto-create-today',
                'schedule' => 'Daily at 07:00 AM ET',
                'description' => 'Auto-creates classroom sessions for today\'s courses',
                'log_file' => 'classroom-auto-create.log',
                'is_active' => true,
                'category' => 'Classroom Management'
            ],
            [
                'name' => 'Generate Course Dates',
                'command' => 'course:generate-dates --days=5 --cleanup --cleanup-days=30',
                'schedule' => 'Weekly on Sunday at 10:00 PM ET',
                'description' => 'Generates CourseDate records for the upcoming week',
                'log_file' => 'course-date-generation.log',
                'is_active' => true,
                'category' => 'Course Management'
            ],
            [
                'name' => 'Close Classroom Sessions',
                'command' => 'classrooms:close-sessions',
                'schedule' => 'Hourly between 12:00 AM - 06:00 AM ET',
                'description' => 'Closes classroom sessions after 12 AM the day after class date',
                'log_file' => 'classroom-session-closure.log',
                'is_active' => true,
                'category' => 'Classroom Management'
            ]
        ];

        foreach ($knownJobs as $job) {
            $jobs[] = array_merge($job, [
                'id' => md5($job['command']),
                'last_run' => $this->getLastRunTime($job['log_file']),
                'next_run' => $this->getNextRunTime($job['schedule']),
                'status' => $this->getJobStatus($job['log_file']),
                'log_size' => $this->getLogFileSize($job['log_file'])
            ]);
        }

        return $jobs;
    }

    /**
     * Get recent cron execution logs
     */
    private function getRecentCronLogs(): array
    {
        $logs = [];
        $logFiles = [
            'course-date-activation.log',
            'classroom-auto-create.log', 
            'course-date-generation.log',
            'classroom-session-closure.log'
        ];

        foreach ($logFiles as $logFile) {
            $filePath = storage_path("logs/{$logFile}");
            if (file_exists($filePath)) {
                $content = file_get_contents($filePath);
                $lines = array_filter(explode("\n", $content));
                
                foreach (array_slice($lines, -10) as $line) {
                    if (preg_match('/\[(.*?)\]/', $line, $matches)) {
                        $logs[] = [
                            'log_file' => $logFile,
                            'executed_at' => $matches[1] ?? 'Unknown',
                            'message' => $line,
                            'type' => $this->getLogType($line)
                        ];
                    }
                }
            }
        }

        // Sort by execution time (most recent first)
        usort($logs, function($a, $b) {
            return strtotime($b['executed_at']) - strtotime($a['executed_at']);
        });

        return array_slice($logs, 0, 20);
    }

    /**
     * Get last run time for a job
     */
    private function getLastRunTime(string $logFile): ?string
    {
        $filePath = storage_path("logs/{$logFile}");
        if (!file_exists($filePath)) {
            return null;
        }

        $content = file_get_contents($filePath);
        $lines = array_filter(explode("\n", $content));
        
        if (empty($lines)) {
            return null;
        }

        $lastLine = end($lines);
        if (preg_match('/\[(.*?)\]/', $lastLine, $matches)) {
            return Carbon::parse($matches[1])->format('M j, Y g:i A');
        }

        return null;
    }

    /**
     * Get next run time estimate
     */
    private function getNextRunTime(string $schedule): string
    {
        if (str_contains($schedule, 'Daily at 06:00 AM')) {
            $next = Carbon::tomorrow()->setTime(6, 0);
        } elseif (str_contains($schedule, 'Daily at 07:00 AM')) {
            $next = Carbon::tomorrow()->setTime(7, 0);
        } elseif (str_contains($schedule, 'Sunday at 10:00 PM')) {
            $next = Carbon::now()->next(Carbon::SUNDAY)->setTime(22, 0);
        } else {
            return 'Unknown';
        }

        return $next->format('M j, Y g:i A');
    }

    /**
     * Get job status based on recent logs
     */
    private function getJobStatus(string $logFile): string
    {
        $filePath = storage_path("logs/{$logFile}");
        if (!file_exists($filePath)) {
            return 'unknown';
        }

        $content = file_get_contents($filePath);
        $lines = array_filter(explode("\n", $content));
        
        if (empty($lines)) {
            return 'unknown';
        }

        $recentLines = array_slice($lines, -5);
        foreach ($recentLines as $line) {
            if (str_contains(strtolower($line), 'error') || str_contains(strtolower($line), 'failed')) {
                return 'error';
            }
        }

        return 'success';
    }

    /**
     * Get log file size
     */
    private function getLogFileSize(string $logFile): string
    {
        $filePath = storage_path("logs/{$logFile}");
        if (!file_exists($filePath)) {
            return '0 KB';
        }

        $bytes = filesize($filePath);
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get log type based on content
     */
    private function getLogType(string $line): string
    {
        $line = strtolower($line);
        
        if (str_contains($line, 'error') || str_contains($line, 'failed')) {
            return 'error';
        } elseif (str_contains($line, 'warning') || str_contains($line, 'warn')) {
            return 'warning';
        } elseif (str_contains($line, 'success') || str_contains($line, 'completed')) {
            return 'success';
        }
        
        return 'info';
    }

    /**
     * Manually trigger a cron job
     */
    public function triggerJob(Request $request): JsonResponse
    {
        $command = $request->input('command');
        
        if (!$command) {
            return response()->json([
                'success' => false,
                'message' => 'Command is required'
            ], 400);
        }

        try {
            Log::info("Manually triggering cron job: {$command}");
            
            Artisan::call($command);
            $output = Artisan::output();
            
            return response()->json([
                'success' => true,
                'message' => 'Job executed successfully',
                'output' => $output
            ]);

        } catch (\Exception $e) {
            Log::error("Error triggering cron job {$command}: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to execute job: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * View log file contents
     */
    public function viewLogs(Request $request): JsonResponse
    {
        $logFile = $request->input('log_file');
        
        if (!$logFile) {
            return response()->json([
                'success' => false,
                'message' => 'Log file is required'
            ], 400);
        }

        $filePath = storage_path("logs/{$logFile}");
        
        if (!file_exists($filePath)) {
            return response()->json([
                'success' => false,
                'message' => 'Log file not found'
            ], 404);
        }

        try {
            $content = file_get_contents($filePath);
            $lines = explode("\n", $content);
            
            // Get last 100 lines
            $recentLines = array_slice($lines, -100);
            
            return response()->json([
                'success' => true,
                'content' => implode("\n", $recentLines),
                'total_lines' => count($lines),
                'file_size' => $this->getLogFileSize($logFile)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to read log file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear log file
     */
    public function clearLogs(Request $request): JsonResponse
    {
        $logFile = $request->input('log_file');
        
        if (!$logFile) {
            return response()->json([
                'success' => false,
                'message' => 'Log file is required'
            ], 400);
        }

        $filePath = storage_path("logs/{$logFile}");
        
        try {
            if (file_exists($filePath)) {
                file_put_contents($filePath, '');
            }
            
            Log::info("Cleared log file: {$logFile}");
            
            return response()->json([
                'success' => true,
                'message' => 'Log file cleared successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,  
                'message' => 'Failed to clear log file: ' . $e->getMessage()
            ], 500);
        }
    }
}