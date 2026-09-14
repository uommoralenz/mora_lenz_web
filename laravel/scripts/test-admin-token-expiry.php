<?php

// Isolated regression check: no production database or network access.
require dirname(__DIR__).'/vendor/autoload.php';
$app = require dirname(__DIR__).'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
Illuminate\Support\Facades\DB::purge('sqlite');

use App\Models\Admin;
use App\Models\AdminToken;
use App\Support\AdminAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

function check(bool $condition, string $message): void
{
    if (! $condition) {
        throw new RuntimeException($message);
    }
    echo "PASS: {$message}\n";
}

try {
    (require dirname(__DIR__).'/database/migrations/2026_01_01_000100_create_admins_table.php')->up();
    (require dirname(__DIR__).'/database/migrations/2026_01_01_000200_create_admin_tokens_table.php')->up();
    config(['moralenz.token_lifetime_hours' => 12]);
    Carbon::setTestNow(Carbon::parse('2026-09-14 21:45:00', config('app.timezone')));
    $admin = Admin::create(['name' => 'Test', 'username' => 'test', 'password' => 'test-only-password', 'is_active' => true]);
    $issued = AdminAuth::issueToken($admin);
    $expiry = AdminToken::first()->getRawOriginal('expires_at');
    $request = Request::create('/api/admin/auth/me');
    $request->headers->set('Authorization', 'Bearer '.$issued['token']);

    DB::enableQueryLog();
    check(AdminAuth::resolve($request)?->id === $admin->id, 'first verification succeeds');
    $updates = array_values(array_filter(DB::getQueryLog(), fn ($entry) => str_starts_with(strtolower($entry['query']), 'update')));
    check(count($updates) === 1 && str_contains($updates[0]['query'], '"expires_at" = ?') && in_array($expiry, $updates[0]['bindings'], true), 'last-used SQL explicitly preserves the original expiry');

    Carbon::setTestNow(Carbon::now()->addSeconds(2));
    check(AdminAuth::resolve($request)?->id === $admin->id, 'dashboard verification succeeds after redirect');
    Carbon::setTestNow(Carbon::now()->addMinutes(2));
    check(AdminAuth::resolve($request)?->id === $admin->id, 'later last-used update keeps the session valid');
    check(AdminToken::first()->getRawOriginal('expires_at') === $expiry, 'activity does not extend or shorten expiry');
    Carbon::setTestNow(Carbon::now()->addHours(13));
    check(AdminAuth::resolve($request) === null, 'genuinely expired sessions are rejected');
    check(AdminToken::count() === 0, 'expired token is deleted');
} finally {
    Carbon::setTestNow();
}
