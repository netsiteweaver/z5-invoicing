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
            $view->with('companySettings', $companySettings);
        });
    }
}
