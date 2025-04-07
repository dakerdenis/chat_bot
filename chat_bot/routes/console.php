<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Регистрация команды
Artisan::starting(function ($artisan) {
    $artisan->resolve(\App\Console\Commands\CleanOldClientLogs::class);
});

// Настройка расписания
Schedule::command('logs:clean-old')->daily();