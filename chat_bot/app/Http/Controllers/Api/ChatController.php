<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Log;
use Exception;

class ChatController extends Controller
{
    public function handle(Request $request)
    {
        if (!$request->expectsJson()) {
            return response()->json(['error' => 'Требуется Content-Type: application/json'], 406);
        }

        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $client = $request->get('client');

        if ($client->dialog_used >= $client->dialog_limit) {
            return response()->json([
                'error' => 'Превышен лимит диалогов. Обновите тариф или обратитесь в поддержку.'
            ], 429);
        }

        $userMessage = $request->input('message');

        // ✅ Инкремент лимита и обновление времени
        $client->increment('dialog_used');
        $client->update(['last_active_at' => now()]);
        $logPayload = ['message' => $userMessage];

try {
    // 💬 Собираем промты
    $prompts = DB::table('client_prompts')
        ->where('client_id', $client->id)
        ->limit(5)
        ->get();

    $messages = [
        [
            'role' => 'system',
            'content' => 'You are a helpful assistant. Answer briefly and clearly. Do not exceed 80–100 words. Structure replies in short bullet points if needed. Avoid lengthy explanations unless explicitly asked. Answer on language on which client asked. and dont use * symbols',
        ]
    ];

    foreach ($prompts as $prompt) {
        $messages[] = ['role' => 'system', 'content' => $prompt->content];
    }

    $messages[] = ['role' => 'user', 'content' => $userMessage];

    $response = OpenAI::chat()->create([
        'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
        'messages' => $messages,
    ]);

    $aiResponse = trim($response->choices[0]->message->content ?? '[Пустой ответ от AI]');
} catch (Exception $e) {
    Log::error('[AI Error]', [
        'message' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
        'client_id' => $client->id ?? null,
        'input_message' => $userMessage,
        'openai_messages' => $messages, // 👈 логируем полезный контекст
    ]);

    return response()->json([
        'error' => 'AI_EXCEPTION',
        'message' => 'Произошла ошибка при обращении к AI',
        'details' => $e->getMessage(), // 👈 удобно для отладки (убрать в проде)
    ], 500);
}


// ✅ В любом случае — одинарный лог
DB::table('client_usage_logs')->insert([
    'client_id' => $client->id,
    'endpoint' => '/api/chat',
    'method' => 'POST',
    'payload' => json_encode($logPayload, JSON_UNESCAPED_UNICODE),
    'ip_address' => $request->ip(),
    'created_at' => now(),
]);

return response()->json([
    'client_id' => $client->id,
    'dialog_used' => $client->dialog_used,
    'answer' => $aiResponse,
]);
    }
}
