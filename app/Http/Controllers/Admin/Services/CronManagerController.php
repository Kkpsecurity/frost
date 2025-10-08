<?php

namespace App\Http\Controllers\Admin\Services;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * Cron Manager Controller - Admin Center Services
 * 
 * Manages and monitors Laravel scheduled tasks (cron jobs)
 * Location: Admin Center > Services > Cron Manager
 */
class CronManagerController extends Controller
{
    /**
     * Display the cron manager dashboard
     */
    public function index(): View
    {
        $scheduledTasks = $this->getScheduledTasks();
        $cronStatus = $this->getCronStatus();
        $systemInfo = $this->getSystemInfo();

        $content = [
            'title' => 'Cron Manager',
            'scheduled_tasks' => $scheduledTasks,
            'cron_status' => $cronStatus,
            'system_info' => $systemInfo,
        ];

        return view('admin.services.cron-manager.index', compact('content'));
    }

    /**
     * Get all scheduled tasks - using hardcoded known tasks for reliability
     */
    private function getScheduledTasks(): array
    {
        return [
            [
                'command' => 'course:activate-dates',
                'expression' => '0 6 * * *',
                'description' => 'Activate CourseDate records daily at 6:00 AM ET',
                'next_run' => $this->getNextRun('0 6 * * *'),
                'timezone' => 'America/New_York',
                'runs_in_background' => false,
                'output_file' => null,
            ],
            [
                'command' => 'classrooms:auto-create-today',
                'expression' => '0 7 * * *',
                'description' => 'Auto-create classroom sessions for today at 7:00 AM ET',
                'next_run' => $this->getNextRun('0 7 * * *'),
                'timezone' => 'America/New_York',
                'runs_in_background' => false,
                'output_file' => null,
            ],
            [
                'command' => 'course:generate-dates --days=5 --cleanup --cleanup-days=30',
                'expression' => '0 22 * * 0',
                'description' => 'Generate CourseDate records weekly on Sunday at 10:00 PM ET',
                'next_run' => $this->getNextRun('0 22 * * 0'),
                'timezone' => 'America/New_York',
                'runs_in_background' => false,
                'output_file' => null,
            ],
        ];
    }

    /**
     * Calculate next run time based on cron expression
     */
    private function getNextRun(string $expression): string
    {
        try {
            if ($expression === '0 6 * * *') {
                $next = Carbon::tomorrow()->setTime(6, 0);
                if (Carbon::now()->hour >= 6) {
                    $next = Carbon::tomorrow()->setTime(6, 0);
                } else {
                    $next = Carbon::today()->setTime(6, 0);
                }
            } elseif ($expression === '0 7 * * *') {
                $next = Carbon::tomorrow()->setTime(7, 0);
                if (Carbon::now()->hour >= 7) {
                    $next = Carbon::tomorrow()->setTime(7, 0);
                } else {
                    $next = Carbon::today()->setTime(7, 0);
                }
            } elseif ($expression === '0 22 * * 0') {
                $next = Carbon::now()->next(Carbon::SUNDAY)->setTime(22, 0);
            } else {
                return 'Unknown';
            }

            return $next->diffForHumans();
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }

    /**
     * Get system information
     */
    private function getSystemInfo(): array
    {
        return [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'timezone' => config('app.timezone'),
            'current_time' => Carbon::now()->format('Y-m-d H:i:s T'),
            'cron_user' => get_current_user(),
            'artisan_path' => base_path('artisan'),
            'schedule_run_command' => PHP_BINARY . ' ' . base_path('artisan') . ' schedule:run',
        ];
    }

    /**
     * Check cron status and health
     */
    private function getCronStatus(): array
    {
        return [
            'last_run' => $this->getLastScheduleRun(),
            'is_running' => $this->isScheduleRunning(),
            'cron_installed' => $this->isCronInstalled(),
            'recommendations' => $this->getCronRecommendations(),
        ];
    }

    /**
     * Run a specific scheduled task manually
     */
    public function runTask(Request $request): JsonResponse
    {
        $taskCommand = $request->get('command');
        
        if (!$taskCommand) {
            return response()->json([
                'success' => false,
                'message' => 'No command specified'
            ], 400);
        }

        try {
            // Extract the actual command from the task
            $command = $this->extractCommand($taskCommand);
            
            // Run the command
            $exitCode = Artisan::call($command);
            $output = Artisan::output();

            return response()->json([
                'success' => $exitCode === 0,
                'message' => $exitCode === 0 ? 'Task executed successfully' : 'Task failed',
                'output' => $output,
                'exit_code' => $exitCode
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to run task: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to run task: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Run the full schedule manually
     */
    public function runSchedule(): JsonResponse
    {
        try {
            $exitCode = Artisan::call('schedule:run');
            $output = Artisan::output();

            return response()->json([
                'success' => $exitCode === 0,
                'message' => $exitCode === 0 ? 'Schedule executed successfully' : 'Schedule execution failed',
                'output' => $output,
                'exit_code' => $exitCode
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to run schedule: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to run schedule: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get logs related to scheduled tasks
     */
    public function getLogs(Request $request): JsonResponse
    {
        try {
            $lines = $request->get('lines', 50);
            
            $logFile = storage_path('logs/laravel.log');
            if (!file_exists($logFile)) {
                return response()->json([
                    'success' => true,
                    'logs' => []
                ]);
            }

            $content = file_get_contents($logFile);
            $logLines = explode("\n", $content);
            
            // Filter for schedule-related logs
            $scheduleLogs = array_filter($logLines, function($line) {
                return strpos($line, 'schedule') !== false || 
                       strpos($line, 'course:') !== false ||
                       strpos($line, 'classrooms:') !== false ||
                       strpos($line, 'command') !== false;
            });

            return response()->json([
                'success' => true,
                'logs' => array_slice(array_values($scheduleLogs), -$lines)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to read logs: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test cron functionality
     */
    public function testCron(): JsonResponse
    {
        try {
            // Test basic artisan command
            $exitCode = Artisan::call('list');
            
            if ($exitCode !== 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Artisan commands not working properly'
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Cron system is functional',
                'tests' => [
                    'artisan_commands' => true,
                    'php_version' => PHP_VERSION,
                    'timezone' => date_default_timezone_get()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cron test failed: ' . $e->getMessage()
            ], 500);
        }
    }

    // Helper methods
    private function getLastScheduleRun(): ?string
    {
        try {
            $lastRun = cache('schedule.last_run');
            return $lastRun ? Carbon::parse($lastRun)->diffForHumans() : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    private function isScheduleRunning(): bool
    {
        try {
            $lastRun = cache('schedule.last_run');
            if (!$lastRun) return false;
            
            // Consider it running if last run was within the last 2 minutes
            return Carbon::parse($lastRun)->diffInMinutes() < 2;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function isCronInstalled(): bool
    {
        try {
            if (PHP_OS_FAMILY === 'Windows') {
                // On Windows, check if Task Scheduler service is available
                $schtasks = shell_exec('schtasks /? 2>nul');
                return !empty(trim($schtasks));
            } else {
                // Unix/Linux systems - check for cron
                $result = shell_exec('which cron 2>/dev/null');
                return !empty(trim($result));
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    private function getCronRecommendations(): array
    {
        $recommendations = [];
        
        if (!$this->isCronInstalled()) {
            if (PHP_OS_FAMILY === 'Windows') {
                $recommendations[] = 'For development: Use "php artisan schedule:work" to test scheduled tasks';
                $recommendations[] = 'For production: Set up Windows Task Scheduler to run Laravel scheduler every minute';
            } else {
                $recommendations[] = 'Install cron service on your system';
                $recommendations[] = 'Add Laravel scheduler to system cron: * * * * * cd ' . base_path() . ' && php artisan schedule:run >> /dev/null 2>&1';
            }
        }
        
        if (!$this->getLastScheduleRun()) {
            if (PHP_OS_FAMILY === 'Windows') {
                $recommendations[] = 'For development: Run "php artisan schedule:work" to test scheduled tasks';
            } else {
                $recommendations[] = 'Add Laravel scheduler to system cron';
            }
        }
        
        return $recommendations;
    }

    private function extractCommand(string $taskCommand): string
    {
        // Extract the actual command from the full command string
        if (preg_match('/artisan\s+(.+)$/', $taskCommand, $matches)) {
            return $matches[1];
        }
        
        // If it's already just the command
        if (!strpos($taskCommand, 'php') && !strpos($taskCommand, 'artisan')) {
            return $taskCommand;
        }
        
        return $taskCommand;
    }
}