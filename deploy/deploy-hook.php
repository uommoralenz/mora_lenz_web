<?php

/*
|--------------------------------------------------------------------------
| Deploy hook — upload ONCE by hand to  public_html/deploy-hook/index.php
|--------------------------------------------------------------------------
|
| GitHub Actions uploads one file (~/deploy/release.zip) over FTP, then POSTs
| to this script. It unpacks the zip into ~/laravel and ~/public_html, runs the
| database migrations and clears Laravel's caches. One small upload instead of
| thousands of files, and migrations no longer need the /setup/ URL.
|
| Requires DEPLOY_TOKEN=<long random string> in ~/laravel/.env. Without it
| this script refuses to do anything. The same value is the DEPLOY_TOKEN secret
| in GitHub. This file is NOT part of the release zip, so changing it means
| uploading it by hand again.
|
*/

header('Content-Type: text/plain; charset=utf-8');
header('Cache-Control: no-store');

function fail(int $code, string $message): never
{
    http_response_code($code);
    exit($message."\n");
}

$home = dirname(__DIR__, 2);          // /home/moralenz
$appDir = $home.'/laravel';
$publicDir = $home.'/public_html';
$zipPath = $home.'/deploy/release.zip';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    fail(405, 'POST only.');
}

// Read DEPLOY_TOKEN straight from .env (Laravel is not booted yet).
$expected = '';
$env = @file_get_contents($appDir.'/.env');
if ($env !== false && preg_match('/^DEPLOY_TOKEN=(.*)$/m', $env, $m)) {
    $expected = trim($m[1], " \t\"'\r");
}

$given = $_SERVER['HTTP_X_DEPLOY_TOKEN'] ?? '';

if (strlen($expected) < 32 || ! hash_equals($expected, (string) $given)) {
    // Same answer whether the token is missing, wrong or unset on the server.
    fail(404, 'Not found.');
}

if (! class_exists(ZipArchive::class)) {
    fail(500, 'PHP zip extension is not available.');
}
if (! is_file($zipPath)) {
    fail(500, 'release.zip was not found.');
}

set_time_limit(300);

$zip = new ZipArchive;
if ($zip->open($zipPath) !== true) {
    fail(500, 'release.zip could not be opened.');
}

$skip = ['laravel/.env', 'public_html/uploads/', 'laravel/storage/logs/'];
$written = 0;

for ($i = 0; $i < $zip->numFiles; $i++) {
    $name = $zip->getNameIndex($i);

    // Only the two known roots, no traversal, nothing absolute.
    if (! preg_match('#^(laravel|public_html)/#', $name) || str_contains($name, '..') || str_contains($name, '\\')) {
        continue;
    }

    foreach ($skip as $prefix) {
        if ($name === $prefix || str_starts_with($name, $prefix)) {
            continue 2;
        }
    }

    $target = $home.'/'.$name;

    if (str_ends_with($name, '/')) {
        if (! is_dir($target)) {
            mkdir($target, 0755, true);
        }
        continue;
    }

    if (! is_dir(dirname($target))) {
        mkdir(dirname($target), 0755, true);
    }

    $in = $zip->getStream($name);
    if ($in === false) {
        fail(500, "Could not read {$name} from the zip.");
    }

    // Write beside, then rename, so a request never sees a half-written file.
    $tmp = $target.'.deploy-tmp';
    $out = fopen($tmp, 'wb');
    stream_copy_to_stream($in, $out);
    fclose($in);
    fclose($out);
    rename($tmp, $target);
    $written++;
}

$zip->close();
@unlink($zipPath);

echo "Unpacked {$written} files.\n";

// Opcache would otherwise keep serving the old compiled code for a while.
if (function_exists('opcache_reset')) {
    @opcache_reset();
}

// Boot Laravel from the freshly unpacked code, then migrate and clear caches.
try {
    require $appDir.'/vendor/autoload.php';
    $app = require $appDir.'/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

    $kernel->call('migrate', ['--force' => true]);
    echo "\n[migrate]\n".trim($kernel->output())."\n";

    $kernel->call('optimize:clear');
    echo "\n[optimize:clear]\n".trim($kernel->output())."\n";
} catch (Throwable $e) {
    fail(500, 'Files were unpacked but Laravel failed: '.$e->getMessage());
}

echo "\nDEPLOY OK\n";
