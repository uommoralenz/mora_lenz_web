<?php

/*
|--------------------------------------------------------------------------
| Routing shim — /setup
|--------------------------------------------------------------------------
|
| This server runs nginx with no rewrite rule, so /setup/<key> never reaches
| the front controller — which left the one-time installer, and with it
| `php artisan migrate`, with no way to run at all on a host that has no SSH.
| Any update that adds a database column would then fail on save with a bare
| 500 ("Unknown column …"), because the code expects a column the database
| was never given.
|
| Because this folder physically exists, nginx serves THIS file, and the key
| comes in as a query parameter instead:
|
|     /setup/?key=<SETUP_KEY>
|
| The route itself is still what enforces everything: it 404s unless
| SETUP_KEY is set in .env and matches in constant time. This file only
| rewrites the URL.
|
| DELETE THIS WHOLE FOLDER once nginx gets:
|     location / { try_files $uri $uri/ /index.php?$query_string; }
|
*/

$path = '/setup';

if (isset($_GET['key']) && is_string($_GET['key'])) {
    $key = trim($_GET['key']);

    // The route pattern allows 16-128 of these characters; anything else is
    // dropped rather than passed on, so it 404s like any other bad key.
    if (preg_match('/^[A-Za-z0-9_-]{16,128}$/', $key)) {
        $path .= '/'.$key;
    }

    unset($_GET['key']);
}

require __DIR__.'/../shim.php';
