<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientWebLoginController;
use App\Http\Controllers\AdminLoginController;

Route::middleware('guest')->group(function () {
    Route::get('/client/login', [ClientWebLoginController::class, 'showLoginForm'])->name('client.login');
    Route::post('/client/login', [ClientWebLoginController::class, 'login']);
    Route::get('/admin/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/admin/login', [AdminLoginController::class, 'login']);
});

Route::middleware('admin.auth')->group(function () {
    Route::get('/admin/dashboard', function () {
        $admin = App\Models\Admin::find(session('admin_id'));
        return view('admin.dashboard', compact('admin'));
    });

    Route::post('/admin/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');
});

Route::middleware('client.auth')->group(function () {
    Route::get('/client/dashboard', function () {
        $client = App\Models\Client::find(session('client_id'));
        return view('client.dashboard', compact('client'));
    });

    Route::post('/client/logout', function () {
        session()->forget('client_id');
        return redirect()->route('client.login');
    })->name('client.logout');
});
