<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ClientSessionAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('client_id')) {
            return redirect()->route('client.login');
        }

        return $next($request);
    }
}
