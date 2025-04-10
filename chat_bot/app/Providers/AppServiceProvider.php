<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Pagination\Paginator;

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
    public function boot()
    {
        RateLimiter::for('client-chat', function ($request) {
            $client = $request->get('client');
        
            if (!$client) {
                return Limit::perMinute(10)->by($request->ip());
            }
        
            return Limit::perMinute($client->rate_limit)->by('client:' . $client->id);
        });

            // ✅ Добавим этот вызов
    Paginator::defaultView('pagination::default'); // Базовая HTML-пагинация (без Bootstrap)
        
    }
}
