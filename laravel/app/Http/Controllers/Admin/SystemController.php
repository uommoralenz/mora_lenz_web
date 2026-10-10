<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Database\Migrations\Migrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Throwable;

/**
 * Database updates, run from the admin panel.
 *
 * An update to this site sometimes adds a database column, and the code that
 * follows expects it. On a normal host you would run `php artisan migrate`
 * after uploading. This one has no SSH, so there is the web installer at
 * /setup/<key> instead — but that lives in its own folder under the webroot,
 * and the webroot is a *separate* directory that the deploy steps copy
 * public/ into by hand. A new folder there is easy to miss, and if it is
 * missed there is no way left to apply a migration at all.
 *
 * The symptom is nasty, because nothing looks broken until someone saves:
 * reading a column the database does not have gives null, so pages render
 * fine, while every UPDATE naming that column fails with a bare 500.
 *
 * This endpoint needs no new file in the webroot — it is reached through the
 * admin API folder that is already deployed — and it is behind the same
 * super-admin token as everything else, so it does not need its own key.
 */
class SystemController extends Controller
{
    /** What the database is missing, if anything. */
    public function status(): JsonResponse
    {
        try {
            $pending = $this->pending();
        } catch (Throwable $e) {
            return response()->json([
                'data' => [
                    'ok' => false,
                    'pending' => [],
                    'message' => 'Could not read the migration state: '.$e->getMessage(),
                ],
            ]);
        }

        return response()->json([
            'data' => [
                'ok' => $pending === [],
                'pending' => $pending,
                'message' => $pending === []
                    ? 'The database is up to date.'
                    : count($pending).' database update'.(count($pending) === 1 ? '' : 's')
                        .' have not been applied yet. Saving may fail until they are.',
            ],
        ]);
    }

    /** Apply them, then drop the caches that could still hold the old shape. */
    public function migrate(): JsonResponse
    {
        $before = $this->pending();

        if ($before === []) {
            return response()->json([
                'data' => [
                    'ok' => true,
                    'applied' => [],
                    'output' => 'Nothing to migrate — the database is already up to date.',
                ],
            ]);
        }

        try {
            Artisan::call('migrate', ['--force' => true]);
            $output = trim(Artisan::output());

            // Config, routes and compiled views can all be cached copies made
            // before this change; a stale one would undo the fix.
            Artisan::call('optimize:clear');
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'The database update failed: '.$e->getMessage(),
            ], 500);
        }

        $after = $this->pending();

        return response()->json([
            'data' => [
                'ok' => $after === [],
                'applied' => array_values(array_diff($before, $after)),
                'output' => $output,
            ],
        ]);
    }

    /** Migration names present in the files but not recorded as run. */
    protected function pending(): array
    {
        /** @var Migrator $migrator */
        $migrator = app('migrator');

        // A brand new database has no migrations table yet, so everything is
        // pending rather than nothing.
        $ran = $migrator->repositoryExists()
            ? $migrator->getRepository()->getRan()
            : [];

        $paths = $migrator->paths();
        $paths[] = database_path('migrations');

        // getMigrationFiles() keys each path by its migration name, which is
        // the same name the repository records — so the difference between
        // the two sets is exactly what has not run.
        $files = array_keys($migrator->getMigrationFiles($paths));

        return array_values(array_diff($files, $ran));
    }
}
