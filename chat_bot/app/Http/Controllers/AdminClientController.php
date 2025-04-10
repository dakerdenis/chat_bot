<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ClientDomain;


class AdminClientController extends Controller
{
    public function index()
    {
        $clients = \App\Models\Client::with('domains')->orderBy('id', 'desc')->get();

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
            'domains' => 'required|string',
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

        // 💾 Сохраняем домены
        $domains = array_map('trim', explode(',', $request->domains));
        foreach ($domains as $domain) {
            ClientDomain::create([
                'client_id' => $client->id,
                'domain' => $domain,
            ]);
        }

        return redirect()->route('admin.clients.index')->with('success', 'Клиент создан!');
    }

    public function edit(Client $client)
    {
        $client->load('domains'); // загрузка доменов
        return view('admin.clients.edit', compact('client'));
    }
    public function update(Request $request, Client $client)
    {
        // Если добавляется/редактируется домен — НЕ делать валидацию основной формы
        if (!$request->filled('new_domain') && !$request->filled('edit_domain')) {
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
        }
    
        // ✏️ Обновление существующего домена
        if ($request->filled('edit_domain_id') && $request->filled('edit_domain')) {
            ClientDomain::where('id', $request->edit_domain_id)
                ->where('client_id', $client->id)
                ->update(['domain' => trim($request->edit_domain)]);
        }
    
        // ➕ Добавление нового домена
        if ($request->filled('new_domain')) {
            ClientDomain::create([
                'client_id' => $client->id,
                'domain' => trim($request->new_domain),
            ]);
        }
    
        return back()->with('success', 'Обновление завершено');
    }
    
}
