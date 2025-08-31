<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SellerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        if (!auth()->check() || (!$user->isSeller() && !$user->isAdmin())) {
            return redirect('/')->with('error', 'Acesso negado. Apenas vendedores.');
        }

        return $next($request);
    }
}
