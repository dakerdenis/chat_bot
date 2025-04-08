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
    
        // ✅ Инкремент лимита и лог
        $client->increment('dialog_used');
        $client->update(['last_active_at' => now()]);
    
        DB::table('client_usage_logs')->insert([
            'client_id' => $client->id,
            'endpoint' => '/api/chat',
            'method' => 'POST',
            'payload' => json_encode(['message' => $userMessage]),
            'ip_address' => $request->ip(),
            'created_at' => now(),
        ]);
    
        // 🧠 GPT-4o mini ответ
        try {
            $response = OpenAI::chat()->create([
                'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
                'messages' => [
                    ['role' => 'system', 'content' => 'Ты — дружелюбный помощник. Отвечай кратко, понятно и по делу.'],
                    ['role' => 'user', 'content' => $userMessage],
                ],
            ]);
        
            $aiResponse = trim($response->choices[0]->message->content ?? '[Пустой ответ от AI]');
        } catch (Exception $e) {
            // 🔥 Логируем ошибку в storage/logs/laravel.log
            Log::error('[AI Error]', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'client_id' => $client->id,
                'input_message' => $userMessage,
            ]);
        
            // 🔎 Также можно логнуть в базу, если хочешь
            DB::table('client_usage_logs')->insert([
                'client_id' => $client->id,
                'endpoint' => '/api/chat',
                'method' => 'ERROR',
                'payload' => json_encode(['error' => $e->getMessage()]),
                'ip_address' => $request->ip(),
                'created_at' => now(),
            ]);
        
            return response()->json([
                'error' => 'Ошибка AI-сервиса: ' . $e->getMessage(),
            ], 500);
        }
        
    
        return response()->json([
            'client_id' => $client->id,
            'dialog_used' => $client->dialog_used,
            'answer' => $aiResponse,
        ]);
    } 
}
