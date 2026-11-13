<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Operation;
use App\Observers\OperationObserver;
use App\Models\Auto;
use App\Observers\AutoObserver;

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
        //
        Operation::observe(OperationObserver::class);
        Auto::observe(AutoObserver::class);
    }

}
