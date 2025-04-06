<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Admin;

class AdminSessionAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('admin_id')) {
            return redirect()->route('admin.login')
                             ->withErrors(['session' => 'Сессия истекла.']);
        }

        $admin = Admin::find(session('admin_id'));

        if (!$admin) {
            session()->forget('admin_id');
            return redirect()->route('admin.login')
                             ->withErrors(['session' => 'Нет доступа.']);
        }

        $request->merge(['admin' => $admin]);

        return $next($request);
    }
}
