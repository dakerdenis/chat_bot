<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Client;

class VerifyClientDomain
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->route('token');
        $client = Client::where('api_token', $token)->firstOrFail();

        $referer = $request->headers->get('referer');
        if (!$referer) {
            return response('Unauthorized: no referer', 403);
        }

        $parsed = parse_url($referer);
        $domain = $parsed['host'] ?? null;

        if (!$domain || !$client->domains()->where('domain', $domain)->exists()) {
            return response('Unauthorized domain: ' . $domain, 403);
        }

        return $next($request);
    }
}
