<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\ClientAuthController;

Route::middleware('auth.client')->group(function () {
    Route::get('/me', function (Request $request) {
        return $request->get('client');
    });
});

Route::post('/client/login', [ClientAuthController::class, 'login']);
