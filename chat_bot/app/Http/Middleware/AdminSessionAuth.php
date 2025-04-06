<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Log;
class AdminSessionAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('admin_id')) {
            Log::warning('ADMIN ACCESS DENIED - no session', ['ip' => $request->ip()]);
            return redirect()->route('admin.login')
                             ->withErrors(['session' => 'Сессия истекла.']);
        }
    
        $admin = Admin::find(session('admin_id'));
    
        if (!$admin) {
            session()->forget('admin_id');
            Log::warning('ADMIN ACCESS DENIED - not found', ['ip' => $request->ip()]);
            return redirect()->route('admin.login')
                             ->withErrors(['session' => 'Нет доступа.']);
        }
    
        $request->merge(['admin' => $admin]);
    
        return $next($request);
    }
    
}
