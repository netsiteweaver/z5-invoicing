<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
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
        // Delegate all Blade @can checks to our role/permission system
        Gate::before(function ($user, string $ability, ?array $arguments = null) {
            return method_exists($user, 'hasPermission') && $user->hasPermission($ability) ? true : null;
        });
    }
}
