<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register Excel service provider if not already registered
        if (!app()->bound('excel')) {
            $this->app->register(\Maatwebsite\Excel\ExcelServiceProvider::class);
            $this->app->alias('Excel', \Maatwebsite\Excel\Facades\Excel::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
