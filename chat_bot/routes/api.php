<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\PublicChatController;

Route::middleware(['auth.client', 'throttle:client-chat'])->post('/chat', [\App\Http\Controllers\Api\ChatController::class, 'handle']);

Route::post('/public-chat', [PublicChatController::class, 'handle']);