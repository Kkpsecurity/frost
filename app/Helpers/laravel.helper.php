<?php

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * API-friendly abort function that returns JSON for API requests
 *
 * @param int $code HTTP status code
 * @param string $message Error message
 * @return never
 */
if (!function_exists('apiAbort')) {
    function apiAbort(int $code = 500, string $message = 'Unspecified Error'): never
    {
        if (request()->wantsJson() || request()->expectsJson()) {
            response()->json([
                'error' => true,
                'code' => $code,
                'message' => $message
            ], $code)->throwResponse();
        }

        abort($code, $message);
    }
}


/**
 * Abort with redirect to a specific route
 *
 * @param string $route Route name or URL
 * @param string $message Flash message
 * @param string $type Flash message type
 * @return never
 */
if (!function_exists('abortToRoute')) {
    function abortToRoute(string $route, string $message, string $type = 'error'): never
    {
        throw new HttpResponseException(
            redirect()->route($route)->with($type, $message)
        );
    }
}

/**
 * Abort with redirect to user dashboard
 *
 * @param string $message Flash message
 * @param string $type Flash message type
 * @return never
 */
if (!function_exists('abortToDashboard')) {
    function abortToDashboard(string $message, string $type = 'error'): never
    {
        $user = auth()->user();

        if (!$user) {
            abort(401, 'Unauthorized');
        }

        // Try to get dashboard route based on user type
        $dashboardRoute = match (true) {
            method_exists($user, 'getDashboardRoute') => $user->getDashboardRoute(),
            method_exists($user, 'Dashboard') => $user->Dashboard(),
            $user->hasRole('admin') ?? false => 'admin.dashboard',
            default => 'dashboard'
        };

        throw new HttpResponseException(
            redirect()->route($dashboardRoute)->with($type, $message)
        );
    }
}


/**
 * Development nagging - throws exception in non-production, logs in production
 *
 * @param string $message Warning message
 * @return void
 * @throws Exception
 */
if (!function_exists('nag')) {
    function nag(string $message): void
    {
        if (App::environment('production')) {
            logger()->warning("Development Nag: {$message}", [
                'backtrace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3)
            ]);
        } else {
            throw new Exception("Development Nag: {$message}");
        }
    }
}


/**
 * Convert Collection to options array for select dropdowns
 *
 * @param Collection $collection Laravel collection
 * @param string|array $pluck Fields to pluck (value, key)
 * @param bool $addBlank Add blank option at top
 * @return array
 */
if (!function_exists('collectionToOptions')) {
    function collectionToOptions(Collection $collection, string|array $pluck, bool $addBlank = false): array
    {
        $pluckFields = is_array($pluck) ? $pluck : [$pluck];
        $options = $collection->pluck(...$pluckFields)->toArray();

        return $addBlank ? ['' => '-- Select Option --'] + $options : $options;
    }
}


/**
 * Development tool helper - only runs in non-production environments
 *
 * @param string $action Action to perform
 * @param mixed ...$args Additional arguments
 * @return void
 */
if (!function_exists('devTool')) {
    function devTool(string $action, mixed ...$args): void
    {
        if (!App::environment('production')) {
            // Check if DevTool class exists before calling
            if (class_exists('\\App\\DevTool\\DevTool')) {
                \App\DevTool\DevTool::handle($action, ...$args);
            } else {
                logger()->debug("DevTool called but class not found", [
                    'action' => $action,
                    'args' => $args
                ]);
            }
        }
    }
}


/**
 * Check if the current process is a queue worker
 *
 * @return bool
 */
if (!function_exists('isQueueWorker')) {
    function isQueueWorker(): bool
    {
        if (!App::runningInConsole()) {
            return false;
        }

        $argv = request()->server('argv', []);

        return collect($argv)->contains(function (string $arg) {
            return str_contains($arg, 'queue:') ||
                   str_contains($arg, 'horizon') ||
                   str_contains($arg, 'queue:work') ||
                   str_contains($arg, 'queue:listen');
        });
    }
}


/**
 * Capture dump output as string
 *
 * @param mixed $variable Variable to dump
 * @param bool $disableDebugBar Whether to disable debug bar
 * @return string
 */
if (!function_exists('dumpCapture')) {
    function dumpCapture(mixed $variable, bool $disableDebugBar = false): string
    {
        if ($disableDebugBar && class_exists('\\Barryvdh\\Debugbar\\Facades\\Debugbar')) {
            \Barryvdh\Debugbar\Facades\Debugbar::disable();
        }

        ob_start();
        dump($variable);
        $output = ob_get_clean();

        return $output ?: '';
    }
}

/**
 * Check if running in production environment
 *
 * @return bool
 */
if (!function_exists('isProduction')) {
    function isProduction(): bool
    {
        return App::environment('production');
    }
}

/**
 * Check if running in local/development environment
 *
 * @return bool
 */
if (!function_exists('isDevelopment')) {
    function isDevelopment(): bool
    {
        return App::environment(['local', 'development', 'dev']);
    }
}

/**
 * Get authenticated user or null safely
 *
 * @return \Illuminate\Contracts\Auth\Authenticatable|null
 */
if (!function_exists('currentUser')) {
    function currentUser(): ?\Illuminate\Contracts\Auth\Authenticatable
    {
        return auth()->user();
    }
}

/**
 * Flash message helper with automatic session flash
 *
 * @param string $message Message to flash
 * @param string $type Message type (success, error, warning, info)
 * @return void
 */
if (!function_exists('flashMessage')) {
    function flashMessage(string $message, string $type = 'info'): void
    {
        session()->flash($type, $message);
    }
}

/**
 * Format date using US format patterns
 *
 * @param mixed $date Date to format (Carbon instance, string, etc.)
 * @param string $format Format type from RoleManager date formats
 * @return string|null Formatted date string or null if date is empty
 */
if (!function_exists('formatUsDate')) {
    function formatUsDate($date, string $format = 'medium_date'): ?string
    {
        return \App\Support\RoleManager::formatDate($date, $format);
    }
}
