<?php

namespace App\Http\Controllers\Admin\AdminCenter;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Admin;
use App\Traits\PageMetaDataTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class CenterController extends Controller
{
    use PageMetaDataTrait;

    public function dashboard()
    {
        // Get all services data
        $services = $this->getServicesData();

        // Get system overview
        $systemOverview = $this->getSystemOverview();

        // Get quick stats
        $quickStats = $this->getQuickStats();

        $content = array_merge([
            'title' => 'Admin Center Dashboard',
            'description' => 'Comprehensive administration dashboard for all system services',
            'services' => $services,
            'system_overview' => $systemOverview,
            'quick_stats' => $quickStats,
        ], self::renderPageMeta('admin_center_dashboard'));

        return view('admin.center.dashboard', compact('content'));
    }

    /**
     * Get all admin services data
     */
    private function getServicesData()
    {
        return [
            'admin_users' => [
                'title' => 'Admin Users',
                'description' => 'Manage administrator accounts and permissions',
                'icon' => 'fas fa-user-shield',
                'route' => 'admin.admin-center.admin-users.index',
                'color' => 'primary',
                'count' => Admin::count(),
                'status' => 'active',
                'last_activity' => $this->getAdminLastActivity(),
                'actions' => [
                    ['text' => 'View All', 'route' => 'admin.admin-center.admin-users.index', 'icon' => 'fas fa-list'],
                    ['text' => 'Add New', 'route' => 'admin.admin-center.admin-users.create', 'icon' => 'fas fa-plus'],
                ]
            ],
            'site_settings' => [
                'title' => 'Site Settings',
                'description' => 'Configure system settings and application preferences',
                'icon' => 'fas fa-wrench',
                'route' => 'admin.settings.index',
                'color' => 'success',
                'count' => DB::table('settings')->count(),
                'status' => 'configured',
                'last_activity' => 'Configuration dependent',
                'actions' => [
                    ['text' => 'View Settings', 'route' => 'admin.settings.index', 'icon' => 'fas fa-cog'],
                    ['text' => 'AdminLTE Config', 'route' => 'admin.settings.adminlte', 'icon' => 'fas fa-palette'],
                ]
            ],
            'payments' => [
                'title' => 'Payment Systems',
                'description' => 'Manage PayPal, Stripe and payment configurations',
                'icon' => 'fas fa-credit-card',
                'route' => 'admin.payments.index',
                'color' => 'warning',
                'count' => 2, // PayPal + Stripe
                'status' => $this->getPaymentStatus(),
                'last_activity' => 'Configuration dependent',
                'actions' => [
                    ['text' => 'Payment Config', 'route' => 'admin.payments.index', 'icon' => 'fas fa-dollar-sign'],
                    ['text' => 'Test Connection', 'url' => '#', 'icon' => 'fas fa-check-circle'],
                ]
            ],
            'media_manager' => [
                'title' => 'Media Manager',
                'description' => 'Manage files, images and media assets',
                'icon' => 'fas fa-hdd',
                'route' => 'admin.media-manager.index',
                'color' => 'info',
                'count' => $this->getMediaCount(),
                'status' => 'operational',
                'last_activity' => $this->getLastMediaActivity(),
                'actions' => [
                    ['text' => 'Browse Files', 'route' => 'admin.media-manager.index', 'icon' => 'fas fa-folder'],
                    ['text' => 'Upload', 'url' => '#', 'icon' => 'fas fa-upload'],
                ]
            ],
            'services' => [
                'title' => 'System Services',
                'description' => 'Cron jobs, queues, cache and system utilities',
                'icon' => 'fas fa-server',
                'route' => 'admin.services.cron-manager.index',
                'color' => 'secondary',
                'count' => $this->getServicesCount(),
                'status' => $this->getServicesStatus(),
                'last_activity' => $this->getLastCronRun(),
                'actions' => [
                    ['text' => 'Cron Manager', 'route' => 'admin.services.cron-manager.index', 'icon' => 'fas fa-clock'],
                    ['text' => 'System Health', 'url' => '#', 'icon' => 'fas fa-heartbeat'],
                ]
            ],
        ];
    }

    /**
     * Get system overview data
     */
    private function getSystemOverview()
    {
        return [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'timezone' => config('app.timezone'),
            'environment' => config('app.env'),
            'debug_mode' => config('app.debug'),
            'maintenance_mode' => app()->isDownForMaintenance(),
            'database_connection' => $this->checkDatabaseConnection(),
            'storage_disk' => config('filesystems.default'),
            'cache_driver' => config('cache.default'),
            'session_driver' => config('session.driver'),
        ];
    }

    /**
     * Get quick statistics
     */
    private function getQuickStats()
    {
        return [
            'total_users' => User::count(),
            'admin_users' => Admin::count(),
            'total_settings' => DB::table('settings')->count(),
            'disk_usage' => $this->getDiskUsage(),
            'uptime' => $this->getSystemUptime(),
            'last_backup' => $this->getLastBackup(),
        ];
    }

    /**
     * Helper methods for service data
     */
    private function getPaymentStatus()
    {
        // Check if payment gateways are configured
        $paypal = config('services.paypal.client_id');
        $stripe = config('services.stripe.key');

        if ($paypal && $stripe)
            return 'fully_configured';
        if ($paypal || $stripe)
            return 'partially_configured';
        return 'not_configured';
    }

    private function getMediaCount()
    {
        try {
            $files = Storage::allFiles('public');
            // Filter out common system files
            $mediaFiles = array_filter($files, function ($file) {
                $name = basename($file);
                return !in_array($name, ['.gitignore', '.DS_Store', 'Thumbs.db']);
            });
            return count($mediaFiles);
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getLastMediaActivity()
    {
        try {
            $files = Storage::allFiles('public');
            if (empty($files))
                return 'No activity';

            $latest = 0;
            foreach ($files as $file) {
                try {
                    $time = Storage::lastModified($file);
                    if ($time > $latest)
                        $latest = $time;
                } catch (\Exception $e) {
                    continue; // Skip files we can't read
                }
            }

            if ($latest === 0)
                return 'No activity';

            return Carbon::createFromTimestamp($latest)->diffForHumans();
        } catch (\Exception $e) {
            return 'Activity unknown';
        }
    }

    private function getServicesCount()
    {
        // Count of available services (cron, cache, queue, etc.)
        return 4; // Cron, Cache, Queue, Logs
    }

    private function getServicesStatus()
    {
        try {
            // Check if schedule is working
            $lastRun = cache('schedule.last_run');
            return $lastRun ? 'running' : 'inactive';
        } catch (\Exception $e) {
            return 'unknown';
        }
    }

    private function getLastCronRun()
    {
        try {
            $lastRun = cache('schedule.last_run');
            return $lastRun ? Carbon::parse($lastRun)->diffForHumans() : 'Never';
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }

    private function checkDatabaseConnection()
    {
        try {
            DB::connection()->getPdo();
            return 'connected';
        } catch (\Exception $e) {
            return 'disconnected';
        }
    }

    private function getDiskUsage()
    {
        try {
            $bytes = disk_free_space('/');
            return $this->formatBytes($bytes);
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }

    private function getSystemUptime()
    {
        try {
            // For Windows/Laravel, we'll use application uptime approximation
            $uptime = time() - filemtime(base_path('bootstrap/cache/config.php'));
            return $this->formatUptime($uptime);
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }

    private function getLastBackup()
    {
        // This would depend on your backup strategy
        return 'Manual backup required';
    }

    private function formatBytes($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, 2) . ' ' . $units[$pow];
    }

    private function formatUptime($seconds)
    {
        $days = floor($seconds / 86400);
        $hours = floor(($seconds % 86400) / 3600);
        $minutes = floor(($seconds % 3600) / 60);

        if ($days > 0)
            return $days . 'd ' . $hours . 'h';
        if ($hours > 0)
            return $hours . 'h ' . $minutes . 'm';
        return $minutes . 'm';
    }

    public function server_logs(Request $request)
    {
        $logFile = storage_path("logs/laravel.log");

        $perPage = 40;
        $current_page = $request->get('page') ?: 1;
        $starting_line = ($current_page - 1) * $perPage;

        $logs = [];
        // if (File::exists($logFile)) {
        //     $totalLines = intval(shell_exec("wc -l < " . escapeshellarg($logFile)));
        //     $lines = collect(File::lines($logFile))->take(-200);

        //     $logPattern = '/\[(?P<timestamp>\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] (?P<environment>\w+).(?P<log_level>\w+): (?P<message>.+?) {\"(?P<key>.+?)\":(?P<value>.+?),(\"exception\":(?P<exception>.+?))?(\n\[stacktrace\]\n(?P<stacktrace>.*?))?\n\n/';

        //     foreach ($lines as $line) {
        //         if (preg_match($logPattern, $line, $matches)) {
        //             $logEntry = [
        //                 'timestamp' => $matches['timestamp'],
        //                 'environment' => $matches['environment'],
        //                 'log_level' => $matches['log_level'],
        //                 'message' => $matches['message'],
        //                 'key' => $matches['key'],
        //                 'value' => $matches['value'],
        //                 'exception' => isset($matches['exception']) ? $matches['exception'] : null,
        //                 'stacktrace' => isset($matches['stacktrace']) ? $matches['stacktrace'] : null
        //             ];
        //             $logs[] = $logEntry;
        //         } else {
        //             $logs[] = ['raw' => $line]; // In case some lines don't match the pattern
        //         }
        //      }

        //     $logs = new LengthAwarePaginator($logs, $totalLines, $perPage, $current_page, [
        //         'path' => $request->url(),
        //         'query' => $request->query(),
        //     ]);
        // }

        //  $content = array_merge([], self::renderPageMeta('admin_center_server_logs'));
        // return view('admin.center.server_logs', compact('content', 'logs'));
    }

    public function impersonate($id)
    {
        // Get the user instance
        $userToImpersonate = User::find($id);

        // Current logged-in user will impersonate the given user
        auth()->user()->impersonate($userToImpersonate);

        return redirect()->route('classroom.dashboard');
    }

    private function getAdminLastActivity()
    {
        try {
            // Try to get the latest admin based on updated_at if it exists
            $admin = Admin::orderBy('updated_at', 'desc')->first();
            return $admin && $admin->updated_at ? $admin->updated_at->diffForHumans() : 'No activity';
        } catch (\Exception $e) {
            // If updated_at doesn't exist, try created_at or just return static text
            try {
                $admin = Admin::orderBy('created_at', 'desc')->first();
                return $admin ? 'Admin account exists' : 'No admins';
            } catch (\Exception $e2) {
                return 'Activity unknown';
            }
        }
    }
}