<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Guards the analytics API with a shared Bearer token (MGT_API_TOKEN).
 */
class VerifyApiToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $expected = (string) config('security_analytics.api_token');
        $given    = (string) $request->bearerToken();

        if ($expected === '' || $given === '' || ! hash_equals($expected, $given)) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        return $next($request);
    }
}
