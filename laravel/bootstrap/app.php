<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // The one-time installer, with NO middleware: it has to run before
            // APP_KEY exists, and the web group cannot encrypt cookies without
            // one. It 404s unless SETUP_KEY is set in .env.
            Route::middleware([])->group(base_path('routes/setup.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Named middleware used by the admin API routes.
        $middleware->alias([
            'admin.auth' => \App\Http\Middleware\AuthenticateAdmin::class,
            'admin.super' => \App\Http\Middleware\EnsureSuperAdmin::class,
        ]);

        // The admin panel is a separate origin (Vercel), so the API is stateless:
        // it authenticates with a bearer token, never a session cookie. That is
        // why Sanctum's stateful middleware is deliberately NOT enabled here.
        //
        // CORS (HandleCors) is already in the global stack and reads config/cors.php.
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Always answer the admin API in JSON, never with an HTML error page.
        $exceptions->shouldRenderJsonWhen(function (Request $request) {
            return $request->is('api/*') || $request->expectsJson();
        });
    })->create();
