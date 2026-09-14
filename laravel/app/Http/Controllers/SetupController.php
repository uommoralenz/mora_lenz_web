<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * One-time web installer, for hosting where you have no shell access.
 *
 * CWP shared hosting usually gives you FTP and a file manager but no SSH, so
 * `php artisan migrate` is not available. Visiting /setup/<SETUP_KEY> once does
 * the same work through the browser.
 *
 * Security:
 *  - Disabled entirely unless SETUP_KEY is set in .env (blank => 404).
 *  - The key is compared in constant time.
 *  - Deliberately registered with NO middleware, so it still works before
 *    APP_KEY exists (the normal web group needs the key to encrypt cookies).
 *  - BLANK OUT SETUP_KEY IN .env THE MOMENT SETUP FINISHES.
 */
class SetupController extends Controller
{
    public function run(Request $request, string $key): Response
    {
        $expected = (string) config('moralenz.setup_key');

        // Not configured, or wrong key: behave as if the route does not exist.
        if ($expected === '' || ! hash_equals($expected, $key)) {
            throw new NotFoundHttpException;
        }

        $lines = [];

        // 1. Application key — Laravel cannot encrypt sessions without it.
        if (blank(config('app.key'))) {
            Artisan::call('key:generate', ['--force' => true]);
            $lines[] = '[key:generate] '.trim(Artisan::output());
            $lines[] = 'APP_KEY has been written to your .env file.';
        } else {
            $lines[] = 'APP_KEY already set — skipped.';
        }

        // 2. Create the tables.
        Artisan::call('migrate', ['--force' => true]);
        $lines[] = "\n[migrate]\n".trim(Artisan::output());

        // 3. Create the super admin (and sample content on a fresh database).
        Artisan::call('db:seed', ['--force' => true]);
        $lines[] = "\n[db:seed]\n".trim(Artisan::output());

        // 4. Drop any stale cached config/routes/views.
        Artisan::call('optimize:clear');
        $lines[] = "\n[optimize:clear]\n".trim(Artisan::output());

        $lines[] = str_repeat('=', 66);
        $lines[] = 'SETUP COMPLETE.';
        $lines[] = '';
        $lines[] = 'Now do these two things, in this order:';
        $lines[] = '  1. Edit .env and blank out SETUP_KEY  (SETUP_KEY=)';
        $lines[] = '  2. Sign in to the admin panel and change the super admin password.';
        $lines[] = '';
        $lines[] = 'Until step 1 is done, anyone who guesses this URL can re-run setup.';

        return response(implode("\n", $lines), 200)
            ->header('Content-Type', 'text/plain; charset=utf-8')
            ->header('X-Robots-Tag', 'noindex, nofollow');
    }
}
