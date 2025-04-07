<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class ChatController extends Controller
{
    public function handle(Request $request)
    {
        if (!$request->expectsJson()) {
            return response()->json(['error' => 'Требуется Content-Type: application/json'], 406);
        }
    
        if (!$request->isMethod('post')) {
            return response()->json(['error' => 'Метод не поддерживается'], 405);
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
    
        // ✅ Увеличиваем usage
        $client->increment('dialog_used');
        $client->update(['last_active_at' => now()]);
    
        // ✅ Логируем запрос
        DB::table('client_usage_logs')->insert([
            'client_id' => $client->id,
            'endpoint' => '/api/chat',
            'method' => 'POST',
            'payload' => json_encode(['message' => $request->input('message')]),
            'ip_address' => $request->ip(),
            'created_at' => now(),
        ]);
    
        // 🔧 Заглушка AI
        $userMessage = $request->input('message');
        $aiResponse = "🔧 AI пока не подключён. Ваш вопрос: \"$userMessage\"";
    
        return response()->json([
            'client_id' => $client->id,
            'dialog_used' => $client->dialog_used,
            'answer' => $aiResponse,
        ]);
    }
    
}
