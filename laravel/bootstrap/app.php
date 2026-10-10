<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

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

        /*
         * Tell a signed-in admin what actually broke.
         *
         * With APP_DEBUG off — which is correct for this server — Laravel
         * answers every unhandled exception with a bare "Server Error", and
         * the panel can only show "the server could not complete this
         * request". The real reason sits in storage/logs/laravel.log, which
         * nobody running the club's website is going to read.
         *
         * So for the admin API only, and only once the request has passed
         * token authentication, the reason is included in the response. It is
         * the same person who would be reading the log, and nothing here is
         * reachable without a valid admin token.
         *
         * Exceptions that already carry a meaningful status and message
         * (validation, 401, 404, 413…) are left alone — this is only for the
         * ones that would otherwise be a blank 500.
         */
        $exceptions->render(function (Throwable $e, Request $request) {
            if (! $request->is('api/admin') && ! $request->is('api/admin/*')) {
                return null;
            }

            if ($e instanceof HttpExceptionInterface || $e instanceof ValidationException) {
                return null;
            }

            // setUserResolver() is set by AuthenticateAdmin and survives onto
            // the request, so this is still answerable while rendering.
            if (! $request->user()) {
                return null;
            }

            return response()->json([
                'message' => class_basename($e).': '.$e->getMessage(),
                'detail' => [
                    'exception' => $e::class,
                    'file' => str_replace(base_path().DIRECTORY_SEPARATOR, '', $e->getFile()),
                    'line' => $e->getLine(),
                ],
            ], 500);
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
} elseif (! is_dir(dirname(__DIR__).'/public') && is_dir(dirname(__DIR__, 2).'/public_html')) {
    // Split CWP layout (…/laravel next to …/public_html) with PUBLIC_PATH not
    // set: use the sibling public_html so logos, the hero photo and uploads
    // are found without any .env change.
    $app->usePublicPath(dirname(__DIR__, 2).'/public_html');
}

return $app;
