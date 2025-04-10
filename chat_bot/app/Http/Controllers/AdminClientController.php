<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\Client;
use Illuminate\Http\Request;

class AdminClientController extends Controller
{
    public function index()
    {
        $clients = \App\Models\Client::orderBy('id', 'desc')->get();
    
        return view('admin.clients.index', compact('clients'));
    }
    

    public function create()
    {
        return view('admin.clients.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:clients,email',
            'password' => 'required|string|min:6',
            'plan' => 'required|in:trial,basic,standard,premium',
        ]);

        $planLimits = [
            'trial' => ['dialogs' => 500, 'rate' => 10],
            'basic' => ['dialogs' => 1000, 'rate' => 20],
            'standard' => ['dialogs' => 3000, 'rate' => 30],
            'premium' => ['dialogs' => 6000, 'rate' => 60],
        ];

        $client = Client::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'api_token' => Str::random(64),
            'plan' => $request->plan,
            'dialog_limit' => $planLimits[$request->plan]['dialogs'],
            'dialog_used' => 0,
            'rate_limit' => $planLimits[$request->plan]['rate'],
            'is_active' => true,
            'last_active_at' => null,
        ]);

        return redirect()->route('admin.clients.index')->with('success', 'Клиент создан!');
    }
}
