<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Models\Client;

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

        $client = Client::where('email', $request->email)->first();

        if (!$client || !Hash::check($request->password, $client->password)) {
            return back()->withErrors(['email' => 'Неверный email или пароль.']);
        }

        if (!$client->is_active) {
            return back()->withErrors(['email' => 'Ваш аккаунт заблокирован.']);
        }

        Session::put('client_id', $client->id);

        return redirect('/client/dashboard');
    }
}
