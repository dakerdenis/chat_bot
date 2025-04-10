<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class AdminClientController extends Controller
{
    public function index()
    {
        $clients = \App\Models\Client::orderBy('id', 'desc')->get();

        return view('admin.clients.index', compact('clients'));
    }

    public function show(Client $client)
    {
        $logs = DB::table('client_usage_logs')
            ->where('client_id', $client->id)
            ->orderByDesc('created_at')
            ->paginate(30); // Пагинация
    
        $compressions = DB::table('client_usage_logs')
            ->where('client_id', $client->id)
            ->where('endpoint', '/client/prompts/compress')
            ->count();
    
        return view('admin.clients.show', compact('client', 'logs', 'compressions'));
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

    public function edit(Client $client)
{
    return view('admin.clients.edit', compact('client'));
}
public function update(Request $request, Client $client)
{
    $request->validate([
        'name' => 'required|string|max:100',
        'email' => 'required|email|unique:clients,email,' . $client->id,
        'plan' => 'required|in:trial,basic,standard,premium',
        'password' => 'nullable|string|min:6',
    ]);

    $limits = [
        'trial' => ['dialogs' => 500, 'rate' => 10],
        'basic' => ['dialogs' => 1000, 'rate' => 20],
        'standard' => ['dialogs' => 3000, 'rate' => 30],
        'premium' => ['dialogs' => 6000, 'rate' => 60],
    ];

    $client->update([
        'name' => $request->name,
        'email' => $request->email,
        'plan' => $request->plan,
        'is_active' => $request->has('is_active'),
        'dialog_limit' => $limits[$request->plan]['dialogs'],
        'rate_limit' => $limits[$request->plan]['rate'],
        'password' => $request->filled('password') ? Hash::make($request->password) : $client->password,
    ]);

    return redirect()->route('admin.clients.index')->with('success', 'Клиент обновлён!');
}

}
