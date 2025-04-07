<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

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
    
            // 🧠 Устанавливаем лимиты на основе тарифа
            $limitsByPlan = [
                'basic' => 10,
                'standard' => 30,
                'premium' => 60,
            ];
    
            $limit = $limitsByPlan[$client->plan] ?? 10;
    
            return Limit::perMinute($limit)->by('client:' . $client->id);
        });
    }
}
