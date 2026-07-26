<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

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
        Paginator::useBootstrapFive();

        // Railway (dan platform sejenis) nangani HTTPS di reverse proxy mereka,
        // jadi Laravel perlu dipaksa generate URL/form pakai https:// di production
        // biar gak muncul "mixed content" warning kayak di form login.
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }
}
