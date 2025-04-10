<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientWebLoginController;
use App\Http\Controllers\AdminLoginController;
use App\Models\Client;
use App\Http\Controllers\ClientPromptController;



Route::middleware('guest')->group(function () {
    Route::get('/client/login', [ClientWebLoginController::class, 'showLoginForm'])->name('client.login');
    Route::post('/client/login', [ClientWebLoginController::class, 'login']);
    Route::get('/admin/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/admin/login', [AdminLoginController::class, 'login']);
});
Route::get('/chat-widget/{token}', function ($token) {
    $client = Client::where('api_token', $token)->firstOrFail();
    return view('widget.chat', compact('client'));
});
Route::middleware('admin.auth')->group(function () {
    Route::get('/admin/dashboard', function () {
        $admin = App\Models\Admin::find(session('admin_id'));
        return view('admin.dashboard', compact('admin'));
    });

    Route::post('/admin/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');

    // 📦 Управление клиентами (добавляем сюда)
    Route::prefix('admin')->group(function () {
        Route::get('/clients', [App\Http\Controllers\AdminClientController::class, 'index'])->name('admin.clients.index');
        Route::get('/clients/create', [App\Http\Controllers\AdminClientController::class, 'create'])->name('admin.clients.create');
        Route::post('/clients', [App\Http\Controllers\AdminClientController::class, 'store'])->name('admin.clients.store');
        Route::get('/clients/{client}', [App\Http\Controllers\AdminClientController::class, 'show'])->name('admin.clients.show');
        Route::get('/clients/{client}/edit', [App\Http\Controllers\AdminClientController::class, 'edit'])->name('admin.clients.edit');
        Route::put('/clients/{client}', [App\Http\Controllers\AdminClientController::class, 'update'])->name('admin.clients.update');
        Route::delete('/clients/{client}', [App\Http\Controllers\AdminClientController::class, 'destroy'])->name('admin.clients.destroy');
    });
});


Route::middleware('client.auth')->group(function () {
    Route::get('/client/dashboard', function () {
        $client = App\Models\Client::find(session('client_id'));
        return view('client.dashboard', compact('client'));
    });
    Route::post('/client/prompts', [ClientPromptController::class, 'store'])->name('client.prompts.store');
    Route::delete('/client/prompts/{id}', [ClientPromptController::class, 'destroy'])->name('client.prompts.destroy');
    Route::put('/client/prompts/{id}', [ClientPromptController::class, 'update'])->name('client.prompts.update');

    Route::post('/client/logout', function () {
        session()->forget('client_id');
        return redirect()->route('client.login');
    })->name('client.logout');
    Route::post('/client/prompts/compress', [\App\Http\Controllers\ClientPromptController::class, 'compress'])
        ->middleware('client.auth')
        ->name('client.prompts.compress');
});
