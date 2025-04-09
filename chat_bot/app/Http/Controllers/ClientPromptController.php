<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Client;

class ClientPromptController extends Controller
{    
    public function store(Request $request)
    {
        $client = Client::find(session('client_id')); // или через middleware
    
        $maxPrompts = match ($client->plan) {
            'basic' => 1,
            'standard' => 3,
            'premium' => 5,
        };
    
        $maxLength = match ($client->plan) {
            'basic' => 200,
            'standard' => 300,
            'premium' => 500,
        };
    
        $request->validate([
            'title' => 'required|string|max:100',
            'content' => 'required|string|max:' . $maxLength,
        ]);
    
        $currentPromptCount = DB::table('client_prompts')
            ->where('client_id', $client->id)
            ->count();
    
        if ($currentPromptCount >= $maxPrompts) {
            return back()->withErrors(['limit' => 'Вы достигли лимита промтов по вашему тарифу.']);
        }
    
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
