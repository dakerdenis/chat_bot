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

        $client = Client::find(session('client_id'));

        if (!$client || !$client->is_active) {
            session()->forget('client_id');
            return redirect()->route('client.login')
                             ->withErrors(['session' => 'Доступ запрещён. Аккаунт отключён или удалён.']);
        }

        // 🕒 Обновляем время последней активности
$client->last_active_at = now();
    $client->save();


        // Пробрасываем клиента дальше
        $request->merge(['client' => $client]);

        return $next($request);
    }
}
