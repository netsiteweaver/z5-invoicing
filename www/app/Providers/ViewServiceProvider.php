<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\CompanySetting;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Share company settings with all views
        View::composer('*', function ($view) {
            $companySettings = CompanySetting::getCurrent();
            $appName = config('app.name');
            $displayAppName = is_string($appName) ? str_replace('_', ' ', $appName) : $appName;
            $view->with('companySettings', $companySettings);
            $view->with('displayAppName', $displayAppName);
        });
    }
}
