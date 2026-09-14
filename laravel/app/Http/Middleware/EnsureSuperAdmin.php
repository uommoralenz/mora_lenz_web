<?php

namespace App\Http\Middleware;

use App\Support\AdminAuth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $admin = AdminAuth::current();

        if (! $admin || ! $admin->isSuperAdmin()) {
            return response()->json([
                'message' => 'This action is restricted to the super admin.',
            ], 403);
        }

        return $next($request);
    }
}
