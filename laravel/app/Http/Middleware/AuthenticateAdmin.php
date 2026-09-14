<?php

namespace App\Http\Middleware;

use App\Support\AdminAuth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $admin = AdminAuth::resolve($request);

        if (! $admin) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        AdminAuth::setCurrent($admin);

        // Make the admin available to controllers as $request->admin().
        $request->setUserResolver(fn () => $admin);

        return $next($request);
    }
}
