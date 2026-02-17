<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',  // ⬅️ TAMBAHKAN INI (enable API routes)
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        // ⬅️ TAMBAHKAN INI (exclude CSRF untuk OnlyOffice)
        $middleware->validateCsrfTokens(except: [
            'onlyoffice/*',
            'api/*',  // API routes juga exclude CSRF
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
