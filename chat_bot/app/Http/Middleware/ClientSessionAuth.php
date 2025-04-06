<?php

namespace App\Http\Middleware;

use App\Models\Client;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ClientSessionAuth
{
    public function handle(Request $request, Closure $next)
    {
        Log::info('Middleware ClientSessionAuth отработал');
    
        if (!session()->has('client_id')) {
            return redirect()->route('client.login')
                ->withErrors(['session' => 'Сессия истекла. Пожалуйста, войдите снова.']);
        }
    
        // 🔐 Дополнительная защита по IP
        if (session('client_ip') && session('client_ip') !== $request->ip()) {
            session()->forget('client_id');
            return redirect()->route('client.login')
                ->withErrors(['session' => 'Сессия сброшена по безопасности.']);
        }
    
        $client = Client::find(session('client_id'));
    
        if (!$client || !$client->is_active) {
            session()->forget('client_id');
            return redirect()->route('client.login')
                ->withErrors(['session' => 'Доступ запрещён. Аккаунт отключён или удалён.']);
        }
    
        $client->last_active_at = now();
        $client->save();
    
        $request->merge(['client' => $client]);
    
        return $next($request);
    }
    
}
