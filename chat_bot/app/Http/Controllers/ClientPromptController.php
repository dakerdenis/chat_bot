<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Client;

class ClientPromptController extends Controller
{    
    public function store(Request $request)
    {
        $client = Client::find(session('client_id')); // или через middleware

        // 🔧 Кол-во доступных промтов по тарифу
        $maxPrompts = match ($client->plan) {
            'trial' => 1,
            'basic' => 2,
            'standard' => 4,
            'premium' => 6,
            default => 1,
        };

        // 🔧 Ограничение по длине одного промта
        $maxLength = match ($client->plan) {
            'trial' => 300,
            'basic' => 450,
            'standard' => 600,
            'premium' => 750,
            default => 300,
        };
        

        // 🧪 Валидация
        $request->validate([
            'title' => 'required|string|max:100',
            'content' => 'required|string|max:' . $maxLength,
        ]);

        // 🔍 Подсчёт текущих промтов
        $currentPromptCount = DB::table('client_prompts')
            ->where('client_id', $client->id)
            ->count();

        if ($currentPromptCount >= $maxPrompts) {
            return back()->withErrors(['limit' => 'Вы достигли лимита промтов по вашему тарифу.']);
        }

        // 💾 Сохраняем новый промт
        DB::table('client_prompts')->insert([
            'client_id' => $client->id,
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Промт добавлен!');
    }
}
