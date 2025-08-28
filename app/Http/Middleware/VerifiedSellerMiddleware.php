<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifiedSellerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !auth()->user()->isSeller()) {
            return redirect('/')->with('error', 'Acesso negado. Apenas vendedores.');
        }

        $seller = auth()->user()->sellerProfile;
        if (!$seller || !$seller->canSellProducts()) {
            return redirect('/seller/pending')->with('error', 'Vendedor precisa ser aprovado e conectar Mercado Pago.');
        }

        return $next($request);
    }
}
