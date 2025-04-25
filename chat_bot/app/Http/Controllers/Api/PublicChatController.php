<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Log;
use Exception;

class PublicChatController extends Controller
{
    public function handle(Request $request)
    {
        if (!$request->expectsJson()) {
            return response()->json(['error' => 'Требуется Content-Type: application/json'], 406);
        }

        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $userMessage = $request->input('message');

        try {
            $response = OpenAI::chat()->create([
                'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Ты дружелюбный ассистент, кратко и понятно отвечаешь на вопросы посетителей сайта. Не используй длинные объяснения. Держи ответ до 100 слов.',
                    ],
                    [
                        'role' => 'user',
                        'content' => $userMessage,
                    ]
                ],
            ]);

            $aiResponse = trim($response->choices[0]->message->content ?? '[Пустой ответ от AI]');
        } catch (Exception $e) {
            Log::error('[Public Chat AI Error]', [
                'message' => $e->getMessage(),
                'input' => $userMessage,
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
