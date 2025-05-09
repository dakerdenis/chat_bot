<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'auth.client' => \App\Http\Middleware\AuthenticateClient::class, // API
            'client.auth' => \App\Http\Middleware\ClientSessionAuth::class, // Веб
            'admin.auth' => \App\Http\Middleware\AdminSessionAuth::class, // Супер АДмин
            'verify.client.domain' => \App\Http\Middleware\VerifyClientDomain::class, //домен клиента
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
