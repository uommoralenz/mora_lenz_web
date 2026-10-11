<?php

namespace App\Http\Middleware;

use App\Support\AdminAuth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Usage: ->middleware('admin.can:events'). Super admins always pass.
 */
class EnsureAdminCan
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $admin = AdminAuth::current();

        if (! $admin || ! $admin->hasPermission($permission)) {
            return response()->json([
                'message' => 'You do not have access to this section.',
            ], 403);
        }

        return $next($request);
    }
}
