<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Client;
use OpenAI\Laravel\Facades\OpenAI;

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


    public function compress(Request $request)
    {
        $request->validate([
            'text' => 'required|string|max:1000',
        ]);

        try {
            $response = OpenAI::chat()->create([
                'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
                'messages' => [
                    ['role' => 'system', 'content' => 'Ты помощник, который умеет кратко переформулировать инструкции для ИИ. Уменьши длину, но сохрани суть.'],
                    ['role' => 'user', 'content' => $request->input('text')],
                ],
            ]);

            return response()->json([
                'success' => true,
                'result' => trim($response->choices[0]->message->content),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function destroy($id)
    {
        $clientId = session('client_id');
        DB::table('client_prompts')->where('id', $id)->where('client_id', $clientId)->delete();
        return back()->with('success', 'Промт удалён!');
    }

    public function update(Request $request, $id)
    {
        $client = Client::find(session('client_id'));

        $maxLength = match ($client->plan) {
            'trial' => 300,
            'basic' => 450,
            'standard' => 600,
            'premium' => 750,
            default => 300,
        };

        $request->validate([
            'content' => 'required|string|max:' . $maxLength,
        ]);

        DB::table('client_prompts')
            ->where('id', $id)
            ->where('client_id', $client->id)
            ->update([
                'content' => $request->input('content'),
                'updated_at' => now(),
            ]);

        return back()->with('success', 'Промт обновлён!');
    }
}
