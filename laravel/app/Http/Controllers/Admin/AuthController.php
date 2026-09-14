<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use App\Support\AdminAuth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'username' => ['required', 'string', 'max:60'],
            'password' => ['required', 'string'],
        ]);

        // Throttle by username + IP so one account cannot be brute forced,
        // and one IP cannot spray many accounts.
        $key = 'admin-login:'.mb_strtolower($data['username']).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);

            throw ValidationException::withMessages([
                'username' => "Too many login attempts. Try again in {$seconds} seconds.",
            ])->status(429);
        }

        $admin = Admin::where('username', $data['username'])->first();

        // Always run a hash comparison so a missing user and a wrong password
        // take a similar amount of time.
        $passwordOk = $admin
            ? Hash::check($data['password'], $admin->password)
            : Hash::check($data['password'], '$2y$12$usesomesillystringfoeu1f7.u.KbZXtR7yBQbLsbq3e4eLZNEP.');

        if (! $admin || ! $passwordOk) {
            RateLimiter::hit($key, 900);

            throw ValidationException::withMessages([
                'username' => 'Those credentials do not match our records.',
            ]);
        }

        if (! $admin->is_active) {
            RateLimiter::hit($key, 900);

            throw ValidationException::withMessages([
                'username' => 'This account has been deactivated.',
            ]);
        }

        RateLimiter::clear($key);
        AdminAuth::pruneExpired();

        $issued = AdminAuth::issueToken($admin, $request->userAgent());

        $admin->forceFill(['last_login_at' => now()])->saveQuietly();

        return response()->json([
            'token' => $issued['token'],
            'expires_at' => $issued['expires_at'],
            'admin' => $this->present($admin),
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'admin' => $this->present(AdminAuth::current()),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        AdminAuth::revokeCurrent($request);

        return response()->json(['message' => 'Signed out.']);
    }

    /**
     * Change your own password. Every other session is signed out, and a fresh
     * token is returned so the panel you are using stays signed in.
     */
    public function changePassword(Request $request): JsonResponse
    {
        $admin = AdminAuth::current();

        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:10', 'max:200', 'confirmed'],
        ]);

        if (! Hash::check($data['current_password'], $admin->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'Your current password is incorrect.',
            ]);
        }

        $admin->update(['password' => $data['password']]);

        AdminAuth::revokeAllFor($admin);

        $issued = AdminAuth::issueToken($admin, $request->userAgent());

        return response()->json([
            'message' => 'Password updated. Other devices have been signed out.',
            'token' => $issued['token'],
            'expires_at' => $issued['expires_at'],
            'admin' => $this->present($admin),
        ]);
    }

    protected function present(?Admin $admin): ?array
    {
        if (! $admin) {
            return null;
        }

        return [
            'id' => $admin->id,
            'name' => $admin->name,
            'username' => $admin->username,
            'email' => $admin->email,
            'is_super_admin' => $admin->isSuperAdmin(),
            'is_active' => (bool) $admin->is_active,
            'last_login_at' => $admin->last_login_at?->toIso8601String(),
        ];
    }
}
