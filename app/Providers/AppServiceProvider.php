<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Load custom helpers
        require_once app_path('helpers.php');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS in production (fixes Mixed Content errors)
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Gate before callback - super_admin gets all permissions
        // This makes FilamentShield work properly
        Gate::before(function ($user, $ability) {
            // Super admin bypasses all permission checks
            if ($user->hasRole('super_admin')) {
                return true;
            }

            // For other users, check if they have the permission
            // If they don't have the permission, deny access
            // This is handled automatically by Spatie, so we return null
            // to let Spatie's permission check handle it
            return null;
        });
    }
}
