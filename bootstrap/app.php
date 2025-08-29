<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Marketplace middleware aliases (Laravel 12)
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'seller' => \App\Http\Middleware\SellerMiddleware::class,
            'verified.seller' => \App\Http\Middleware\VerifiedSellerMiddleware::class,
            'security.headers' => \App\Http\Middleware\SecurityHeadersMiddleware::class,
            'rate.limit' => \App\Http\Middleware\RateLimitMiddleware::class,
            'validate.upload' => \App\Http\Middleware\ValidateFileUploadMiddleware::class,
        ]);

        // Aplicar middleware de segurança globalmente
        $middleware->append(\App\Http\Middleware\SecurityHeadersMiddleware::class);
        
        // CSRF protection habilitado por padrão no Laravel 12
        // XSS prevention via escape automático do Blade
        // SQL Injection prevention via Eloquent ORM
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
