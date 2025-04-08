<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Регистрация команды
Artisan::command('logs:clean-old', function () {
    $this->call(\App\Console\Commands\CleanOldClientLogs::class);
});


// Настройка расписания
Schedule::command('logs:clean-old')->daily();