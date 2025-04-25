<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Exception;

class PublicChatController extends Controller
{
    public function handle(Request $request)
    {
        if (!$request->expectsJson()) {
            return response()->json(['error' => 'Требуется Content-Type: application/json'], 406);
        }

        $ip = $request->ip();
        $key = 'public-chat:' . $ip;

        // 🚨 Ограничение: максимум 30 запросов в час с одного IP
        if (RateLimiter::tooManyAttempts($key, 30)) {
            return response()->json(['error' => 'Слишком много запросов. Попробуйте позже.'], 429);
        }
        RateLimiter::hit($key, 3600); // 3600 секунд = 1 час

        $validated = $request->validate([
            'history' => 'required|array|max:10',
            'history.*.role' => 'required|string|in:user,assistant',
            'history.*.content' => 'required|string|max:1000',
        ]);

        $history = $validated['history'];

        try {
            $messages = [];

            $messages[] = [
                'role' => 'system',
                'content' => "You are a professional assistant from DAKER Software Digital Studio. Help users understand and use DAKER One—our AI chatbot for automating 24/7 website support without staff. Be clear, helpful, friendly. Keep replies <100 words unless asked. Use bullet points. Default lang: EN. Switch to RU/AZ if detected. Offer help with pricing, setup, demo, plan selection. (Plans info here).",
            ];

            foreach ($history as $message) {
                $messages[] = [
                    'role' => $message['role'],
                    'content' => strip_tags($message['content']), // Удаляем HTML
                ];
            }

            $response = OpenAI::chat()->create([
                'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
                'messages' => $messages,
            ]);

            $aiResponse = trim($response->choices[0]->message->content ?? '[Пустой ответ от AI]');
        } catch (Exception $e) {
            Log::error('[Public Chat AI Error]', [
                'message' => $e->getMessage(),
                'input' => $history,
            ]);

            return response()->json([
                'error' => 'Произошла ошибка при обращении к AI. Попробуйте позже.'
            ], 500);
        }

        return response()->json([
            'answer' => $aiResponse
        ]);
    }
}
