<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientWebLoginController;

Route::middleware('guest')->group(function () {
    Route::get('/client/login', [ClientWebLoginController::class, 'showLoginForm'])->name('client.login');
    Route::post('/client/login', [ClientWebLoginController::class, 'login']);
});

Route::middleware('client.auth')->group(function () {
    Route::get('/client/dashboard', function () {
        return 'Добро пожаловать в админ-панель клиента!';
    });
});
