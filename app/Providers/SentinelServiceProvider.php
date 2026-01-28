<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Database\Events\QueryExecuted;
use App\Services\Sentinel\SentinelBridgeService;
use App\Listeners\Sentinel\DatabaseQueryListener;
use App\Listeners\Sentinel\StudentOnboardingListener;

class SentinelServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register Sentinel Bridge as singleton
        $this->app->singleton(SentinelBridgeService::class, function ($app) {
            return new SentinelBridgeService();
        });

        // Merge config
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/sentinel.php',
            'sentinel'
        );
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/n8n.php',
            'n8n'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if (!config('sentinel.enabled', false)) {
            return;
        }

        // Register event listeners
        $this->registerEventListeners();

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                \App\Console\Commands\SentinelCleanupCommand::class,
                \App\Console\Commands\SentinelTestCommand::class,
            ]);
        }
    }

    /**
     * Register event listeners
     */
    protected function registerEventListeners(): void
    {
        // Database query listener
        if (config('sentinel.tracking.queries', false)) {
            Event::listen(QueryExecuted::class, DatabaseQueryListener::class);
        }

        // Student onboarding listener
        if (config('sentinel.tracking.events', false)) {
            Event::subscribe(StudentOnboardingListener::class);
        }
    }
}
