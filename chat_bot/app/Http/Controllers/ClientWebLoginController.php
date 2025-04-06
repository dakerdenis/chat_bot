<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Models\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class ClientWebLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('client.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        // 🔐 Rate limiter: max 5 попыток с одного IP в минуту
        $ip = $request->ip();
        $key = 'login:' . $ip;

        if (RateLimiter::tooManyAttempts($key, 5)) {
            return back()->withErrors([
                'email' => 'Слишком много попыток входа. Попробуйте позже.'
            ]);
        }

        RateLimiter::hit($key, 60); // 60 секунд блокировки

        // 🔍 Поиск клиента
        $client = Client::where('email', $request->email)->first();

        if (!$client || !Hash::check($request->password, $client->password)) {
            Log::warning('CLIENT LOGIN FAILED', [
                'email' => $request->email,
                'ip' => $ip,
            ]);

            return back()->withErrors(['email' => 'Неверный email или пароль.']);
        }

        if (!$client->is_active) {
            Log::warning('CLIENT BLOCKED LOGIN ATTEMPT', [
                'email' => $request->email,
                'ip' => $ip,
            ]);

            return back()->withErrors(['email' => 'Ваш аккаунт заблокирован.']);
        }

        // 📌 Обновляем last_active_at
        $client->update(['last_active_at' => now()]);

        // 📝 Лог успешного входа
        Log::info('CLIENT LOGIN SUCCESS', [
            'client_id' => $client->id,
            'email' => $client->email,
            'ip' => $ip,
        ]);

        // 🧠 Сохраняем client_id в сессию
        Session::put('client_id', $client->id);

        return redirect('/client/dashboard');
    }
}
