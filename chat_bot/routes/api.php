<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;


Route::middleware(['auth.client', 'throttle:client-chat'])->post('/chat', [\App\Http\Controllers\Api\ChatController::class, 'handle']);

