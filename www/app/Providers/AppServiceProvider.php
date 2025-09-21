<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\User;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Supplier;
use App\Observers\UserObserver;
use App\Observers\ProductObserver;
use App\Observers\CustomerObserver;
use App\Observers\SupplierObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        User::observe(UserObserver::class);
        Product::observe(ProductObserver::class);
        Customer::observe(CustomerObserver::class);
        Supplier::observe(SupplierObserver::class);
    }
}
