<?php

/*
|--------------------------------------------------------------------------
| Routing shim — /api/admin/*
|--------------------------------------------------------------------------
|
| The admin API has dozens of paths (/events/12, /team/members/3, …) so it
| cannot have a folder each. Instead the Vercel panel sends every call to
|
|     /api/admin/?__path=/events/12&<any real query params>
|
| and this shim turns that back into the URL Laravel routes on. The panel does
| this only when LARAVEL_API_COMPAT=1 is set in its Vercel environment.
|
| DELETE THIS FOLDER (and unset LARAVEL_API_COMPAT) once nginx gets:
|     location / { try_files $uri $uri/ /index.php?$query_string; }
|
*/

$requested = $_GET['__path'] ?? '';
unset($_GET['__path']);

$path = '/api/admin';

if (is_string($requested) && $requested !== '') {
    $requested = '/'.ltrim($requested, '/');

    // Only plain path characters. No dots, so "../" can never appear.
    if (preg_match('#^(/[A-Za-z0-9_-]+)+/?$#', $requested)) {
        $path .= rtrim($requested, '/');
    } else {
        header('Content-Type: application/json');
        http_response_code(400);
        exit(json_encode(['message' => 'Malformed API path.']));
    }
}

require __DIR__.'/../../shim.php';
