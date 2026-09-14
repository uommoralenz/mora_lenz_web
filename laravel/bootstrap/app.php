<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

$app = Application::configure(basePath: dirname(__DIR__))
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
        // Check authentication before resolving resource IDs or disclosing 404s.
        $middleware->prependToPriorityList(
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\AuthenticateAdmin::class,
        );
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

/*
|--------------------------------------------------------------------------
| Where the app's real, served webroot actually is
|--------------------------------------------------------------------------
|
| On CWP shared hosting (see DEPLOYMENT.md) the app is split across two
| directories: the code lives in e.g. moralenz_app/, but the *contents* of
| its public/ folder were copied into a separate public_html/ next to it —
| and public/ itself was then deleted, per the deploy steps.
|
| Left alone, Laravel's public_path() helper still points at
| "<app root>/public", which no longer exists on this server. That silently
| breaks three things: the logo/background file_exists() checks in the Blade
| templates (always false, so the CSS fallback shows forever), and — more
| importantly — every image uploaded through the admin panel, because
| config/filesystems.php's "public_uploads" disk also resolves its root from
| public_path(). Files were being written to a folder nginx never serves.
|
| Set PUBLIC_PATH in .env to the real webroot (e.g. the absolute path to
| public_html) to fix all three at once. Leave it empty for a normal,
| non-split deployment (local dev, or a host that lets public/ stay put) —
| Laravel's default is used unchanged.
|
*/
// env() is normally only safe to call once $app->handleRequest() has started
// bootstrapping the kernel — .env isn't read yet at this point in the file.
// Loading it explicitly here (harmless — the kernel re-runs the same,
// idempotent step later) lets PUBLIC_PATH below actually take effect.
(new LoadEnvironmentVariables())->bootstrap($app);

if ($publicPath = env('PUBLIC_PATH')) {
    $app->usePublicPath($publicPath);
}

return $app;
