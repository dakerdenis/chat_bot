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
                        'content' => "You are a professional assistant from DAKER Software Digital Studio. Help users understand and use DAKER One—our AI chatbot for automating 24/7 website support without staff. Be clear, helpful, friendly. Keep replies <100 words unless asked. Use bullet points. Don't mention you're AI unless asked. Default lang: EN. Switch to RU/AZ if detected. Offer help with pricing, setup, demo, plan selection.\n\nPlans:\nTrial: 1 prompt, 300 chars, 500 dialogs/day, 10 req/min (Free, 7d)\nBasic: 2 prompts, 450 chars, 1000 dialogs, 20 req/min ($19/mo)\nStandard: 4 prompts, 600 chars, 3000 dialogs, 30 req/min ($49/mo)\nPremium: 6 prompts, 750 chars, 6000 dialogs, 60 req/min ($99/mo)\nTrial=test | Basic=small | Standard=team | Premium=high\n\nFeatures:\n- Works on any site/CMS\n- Mobile-friendly\n- Easy setup: <head> snippet\n- Client-written prompts\n- Per-domain prompts\n- RU/AZ/EN supported\n- Handles complex queries\n- Custom tone via prompts\n- Optional chat logs (paid)\n- Warning on limit\n- No KB support\n- No translation (but detects langs)\n\nTech:\n- Fast widget\n- iframe/script modes\n- Token-secured API\n- SPA-ready\n- Show/hide schedule\n- GDPR-safe\n\nAlso from DAKER:\n- Web/app dev\n- UI/UX, branding\n- SaaS/AI tools\n- Full-cycle delivery\n- Support, scaling\n- Risk analysis, budgeting\n\nPhilosophy: clear deadlines, no hidden fees, scalable products, full delivery.\n\nContact: contact@daker.az | +994507506901 / +994553275269 | daker.az\n\nGoal: help users explore DAKER One and guide them to setup/demo/pricing/upgrade."
,
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
