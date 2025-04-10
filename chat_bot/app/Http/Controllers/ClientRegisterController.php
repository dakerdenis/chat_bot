<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientDomain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ClientRegisterController extends Controller
{
    public function showForm()
    {
        return view('auth.client-register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:clients,email',
            'password' => 'required|string|min:6|confirmed',
            'domain' => 'required|string|max:255',
        ]);

        $client = Client::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'api_token' => Str::random(64),
            'plan' => 'trial', // 🆓 По умолчанию
            'dialog_limit' => 500,
            'dialog_used' => 0,
            'rate_limit' => 10,
            'language' => 'ru',
            'is_active' => true,
        ]);

        // 💾 Добавляем домен
        ClientDomain::create([
            'client_id' => $client->id,
            'domain' => $request->domain,
        ]);

        // Вход в систему
        session(['client_id' => $client->id]);

        return redirect()->route('client.dashboard')->with('success', 'Регистрация прошла успешно!');
    }
}
