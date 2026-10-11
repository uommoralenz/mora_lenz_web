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

        // A temporary password must be replaced before anything else works.
        if ($admin->must_change_password
            && ! $request->is('api/admin/auth/me', 'api/admin/auth/logout', 'api/admin/auth/password')) {
            AdminAuth::setCurrent(null);

            return response()->json([
                'message' => 'You must change your temporary password first.',
                'code' => 'password_change_required',
            ], 403);
        }

        try {
            $response = $next($request);
            $response->headers->set('Cache-Control', 'no-store, private');
            return $response;
        } finally {
            AdminAuth::setCurrent(null);
        }
    }
}
