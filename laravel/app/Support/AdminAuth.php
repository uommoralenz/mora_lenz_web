<?php

namespace App\Support;

use App\Models\Admin;
use App\Models\AdminToken;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Minimal bearer-token authentication for the admin API.
 *
 * Deliberately not Sanctum/Passport: the admin panel is a single trusted app on
 * Vercel, there is no OAuth, no public sign-up and no password reset. Keeping it
 * to one table and ~60 lines means there is far less surface to get wrong.
 *
 * The plain token is returned to the panel exactly once, at login. Only its
 * SHA-256 hash is stored, so a leaked database dump cannot be replayed as a login.
 */
class AdminAuth
{
    /** The admin resolved for the current request, if any. */
    protected static ?Admin $current = null;

    /** Issue a fresh token for an admin and persist its hash. */
    public static function issueToken(Admin $admin, ?string $userAgent = null): array
    {
        $plain = Str::random(64);

        $token = AdminToken::create([
            'admin_id' => $admin->id,
            'token_hash' => hash('sha256', $plain),
            'expires_at' => Carbon::now()->addHours(
                max(1, (int) config('moralenz.token_lifetime_hours', 12))
            ),
            'user_agent' => $userAgent ? Str::limit($userAgent, 250, '') : null,
        ]);

        return [
            'token' => $plain,
            'expires_at' => $token->expires_at->toIso8601String(),
        ];
    }

    /**
     * Read the bearer token off the request.
     *
     * This server's nginx does not forward the Authorization header to
     * PHP-FPM by default (a well-known nginx quirk — it needs an explicit
     * `fastcgi_param HTTP_AUTHORIZATION $http_authorization;` line, which
     * requires config access we don't have here). Every other header passes
     * through fine, so the panel also sends the same token as X-Admin-Token
     * and that is the fallback that actually works on this host. Without it,
     * login would succeed (it doesn't need a token) but every very next
     * request — /auth/me, /stats, … — would silently 401 and bounce back to
     * the login page, because the token this server never saw.
     */
    protected static function extractToken(Request $request): ?string
    {
        $plain = $request->bearerToken();

        if ($plain) {
            return $plain;
        }

        $header = $request->header('X-Admin-Token');

        return $header !== null && $header !== '' ? $header : null;
    }

    /** Resolve the admin behind a request's bearer token, or null. */
    public static function resolve(Request $request): ?Admin
    {
        $plain = static::extractToken($request);

        if (! $plain) {
            return null;
        }

        $token = AdminToken::with('admin')
            ->where('token_hash', hash('sha256', $plain))
            ->first();

        if (! $token || $token->hasExpired()) {
            $token?->delete();

            return null;
        }

        $admin = $token->admin;

        // A deactivated or deleted admin loses access immediately, even if
        // their token has not expired yet.
        if (! $admin || ! $admin->is_active) {
            $token->delete();

            return null;
        }

        // Only touch the timestamp once a minute to avoid a write per request.
        if (! $token->last_used_at || $token->last_used_at->lt(Carbon::now()->subMinute())) {
            $token->forceFill(['last_used_at' => Carbon::now()])->saveQuietly();
        }

        return $admin;
    }

    /** Revoke the token used by this request (sign out of this device only). */
    public static function revokeCurrent(Request $request): void
    {
        $plain = static::extractToken($request);

        if ($plain) {
            AdminToken::where('token_hash', hash('sha256', $plain))->delete();
        }
    }

    /** Revoke every token for an admin (used when a password changes). */
    public static function revokeAllFor(Admin $admin): void
    {
        AdminToken::where('admin_id', $admin->id)->delete();
    }

    /** Delete expired tokens so the table does not grow without bound. */
    public static function pruneExpired(): void
    {
        AdminToken::where('expires_at', '<', Carbon::now())->delete();
    }

    public static function setCurrent(?Admin $admin): void
    {
        static::$current = $admin;
    }

    public static function current(): ?Admin
    {
        return static::$current;
    }
}
