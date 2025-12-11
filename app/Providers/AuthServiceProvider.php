<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Validation\Rules\Password;
use App\Services\SiteConfigService;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Configure password rules based on site settings
        $this->configurePasswordRules();
    }

    /**
     * Configure password validation rules based on site settings
     */
    protected function configurePasswordRules(): void
    {
        try {
            $siteConfigService = app(SiteConfigService::class);
            $passwordRequirements = $siteConfigService->getPasswordRequirements();

            $passwordRule = Password::min($passwordRequirements['min_length']);

            if ($passwordRequirements['require_uppercase']) {
                $passwordRule->mixedCase();
            }

            if ($passwordRequirements['require_numbers']) {
                $passwordRule->numbers();
            }

            if ($passwordRequirements['require_symbols']) {
                $passwordRule->symbols();
            }

            Password::defaults(function () use ($passwordRule) {
                return $passwordRule;
            });
        } catch (\Exception $e) {
            // Fallback to default rules if settings are not available
            Password::defaults(function () {
                return Password::min(8);
            });
        }
    }
}
