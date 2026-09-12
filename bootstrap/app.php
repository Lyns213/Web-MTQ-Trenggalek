<?php

use App\Http\Middleware\HttpRedirect;
use App\Http\Middleware\SanitizeReferer;
use App\Http\Middleware\SetHttpOnlyCookies;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\XSS;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // $middleware->append(XSS::class);
        // $middleware->append(HttpRedirect::class);
        // $middleware->append(SanitizeReferer::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
