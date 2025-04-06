<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;


Route::middleware('auth.client')->group(function () {
    Route::get('/me', function (Request $request) {
        return $request->get('client');
    });
});


