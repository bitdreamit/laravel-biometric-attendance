<?php
namespace Bitdreamit\BiometricAttendance\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Validates that the request carries a Sanctum token created specifically
 * as a biometric agent token (name starts with 'biometric-agent').
 * Also enforces rate limiting per token.
 */
class AgentTokenMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Must be authenticated via Sanctum
        if (! $request->user()) {
            return response()->json(['error' => 'Unauthorized — valid Bearer token required'], 401);
        }

        // Token must be a biometric-agent token
        $token = $request->user()->currentAccessToken();
        if (! $token || ! str_starts_with($token->name, 'biometric-agent')) {
            return response()->json([
                'error' => 'Forbidden — use a token created by: php artisan biometric:agent-token'
            ], 403);
        }

        // Simple rate limiting: 1000 requests per minute per token
        $key      = 'biometric_agent_rl:' . $token->id;
        $maxHits  = 1000;
        $decayMin = 1;

        if (class_exists(\Illuminate\Support\Facades\RateLimiter::class)) {
            $limiter = \Illuminate\Support\Facades\RateLimiter::class;
            if ($limiter::tooManyAttempts($key, $maxHits)) {
                return response()->json(['error' => 'Too many requests'], 429);
            }
            $limiter::hit($key, $decayMin * 60);
        }

        return $next($request);
    }
}
