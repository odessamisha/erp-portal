<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Operation;
use App\Observers\OperationObserver;
use App\Models\Auto;
use App\Observers\AutoObserver;
use App\Models\ClientMessage;
use App\Observers\ClientMessageObserver;
 use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //

        if (env(key: 'APP_ENV') !=='local') {
             URL::forceScheme(scheme:'https');
}
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Operation::observe(OperationObserver::class);
        Auto::observe(AutoObserver::class);
        ClientMessage::observe(ClientMessageObserver::class);
    }

    

}
