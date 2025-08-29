<?php
/**
 * Arquivo: app/Http/Middleware/RateLimitMiddleware.php
 * Descrição: Middleware para rate limiting específico do marketplace
 * Laravel Version: 12.x
 * Criado em: 29/08/2025
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class RateLimitMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $key = 'global'): Response
    {
        // Definir limites específicos para diferentes ações
        $limits = [
            'api' => [60, 60], // 60 requests per minute
            'login' => [5, 60], // 5 login attempts per minute
            'checkout' => [3, 60], // 3 checkout attempts per minute
            'cart' => [30, 60], // 30 cart operations per minute
            'search' => [100, 60], // 100 searches per minute
            'global' => [120, 60], // 120 requests per minute global
        ];

        $limit = $limits[$key] ?? $limits['global'];
        $maxAttempts = $limit[0];
        $decayMinutes = $limit[1];

        $rateLimiterKey = $this->resolveRequestSignature($request, $key);

        if (RateLimiter::tooManyAttempts($rateLimiterKey, $maxAttempts)) {
            $retryAfter = RateLimiter::availableIn($rateLimiterKey);
            
            return response()->json([
                'message' => 'Muitas tentativas. Tente novamente em ' . $retryAfter . ' segundos.',
                'retry_after' => $retryAfter
            ], 429)->header('Retry-After', $retryAfter);
        }

        RateLimiter::hit($rateLimiterKey, $decayMinutes);

        $response = $next($request);

        // Adicionar headers informativos
        $remaining = $maxAttempts - RateLimiter::attempts($rateLimiterKey);
        $response->headers->add([
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => max(0, $remaining),
            'X-RateLimit-Reset' => now()->addMinutes($decayMinutes)->timestamp,
        ]);

        return $response;
    }

    /**
     * Resolver a assinatura da requisição para rate limiting
     */
    protected function resolveRequestSignature(Request $request, string $key): string
    {
        $user = $request->user();
        
        if ($user) {
            return "rate_limit:{$key}:user:{$user->id}";
        }

        return "rate_limit:{$key}:ip:" . $request->ip();
    }
}