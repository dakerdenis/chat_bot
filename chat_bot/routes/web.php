<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientWebLoginController;

Route::middleware('guest')->group(function () {
    Route::get('/client/login', [ClientWebLoginController::class, 'showLoginForm'])->name('client.login');
    Route::post('/client/login', [ClientWebLoginController::class, 'login']);
});

Route::middleware('client.auth')->group(function () {
    Route::get('/client/dashboard', function () {
        $client = \App\Models\Client::find(session('client_id'));
        return view('client.dashboard', compact('client'));
    });

    Route::post('/client/logout', function () {
        session()->forget('client_id');
        return redirect()->route('client.login');
    })->name('client.logout');
});
