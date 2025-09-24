<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Register custom user provider for remember me functionality
        Auth::provider('custom', function ($app, $config) {
            return new CustomUserProvider($app['hash'], $config['model']);
        });

        // Delegate all ability checks to our custom permission system
        Gate::before(function ($user, string $ability) {
            try {
                return method_exists($user, 'hasPermission') && $user->hasPermission($ability) ? true : null;
            } catch (\Throwable $e) {
                return null;
            }
        });
    }
}
