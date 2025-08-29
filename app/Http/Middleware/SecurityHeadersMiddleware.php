<?php
/**
 * Arquivo: app/Http/Middleware/SecurityHeadersMiddleware.php
 * Descrição: Middleware para adicionar headers de segurança
 * Laravel Version: 12.x
 * Criado em: 29/08/2025
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Adicionar headers de segurança
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        
        // CSP para marketplace - permitir assets locais, CDNs e Vite (Laragon)
        $csp = "default-src 'self'; " .
               "script-src 'self' 'unsafe-inline' 'unsafe-eval' cdn.tailwindcss.com unpkg.com https://marketplace-b2c.test:5173 https://marketplace-b2c.test:5174; " .
               "style-src 'self' 'unsafe-inline' fonts.googleapis.com cdn.tailwindcss.com https://marketplace-b2c.test:5173 https://marketplace-b2c.test:5174; " .
               "font-src 'self' fonts.gstatic.com; " .
               "img-src 'self' data: blob: *.mercadopago.com *.mlstatic.com; " .
               "connect-src 'self' api.mercadopago.com *.mercadopago.com https://marketplace-b2c.test:5173 https://marketplace-b2c.test:5174 wss://marketplace-b2c.test:5173 wss://marketplace-b2c.test:5174; " .
               "frame-src 'self' *.mercadopago.com";
               
        $response->headers->set('Content-Security-Policy', $csp);
        
        // HSTS para HTTPS em produção
        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}