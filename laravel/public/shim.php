<?php

/*
|--------------------------------------------------------------------------
| Shared shim bootstrap
|--------------------------------------------------------------------------
|
| Included by the small index.php files in public_html/events, /team,
| /services/*, /contact and /api/admin. Each of those sets $path (and may set
| $query) to the URL Laravel should see; this file rewrites the request
| environment and runs the real front controller.
|
| Why the $_SERVER edits matter: Symfony works out its "base URL" by comparing
| basename(SCRIPT_FILENAME) with basename(SCRIPT_NAME). Both are index.php
| here, so it would otherwise treat /events/index.php as the base and mangle
| every generated link. Forcing SCRIPT_NAME to /index.php makes the base URL
| resolve to empty, exactly as it would on a server with working rewrites — so
| route() and url() keep producing clean paths.
|
| DELETE THIS FILE once nginx can rewrite to index.php itself.
|
*/

if (! isset($path) || ! is_string($path)) {
    http_response_code(500);
    exit('Shim misconfigured: no path set.');
}

// Anything still in $_GET is a real query parameter and must be preserved.
$query = http_build_query($_GET);

$_SERVER['REQUEST_URI'] = $path.($query !== '' ? '?'.$query : '');
$_SERVER['QUERY_STRING'] = $query;
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['PHP_SELF'] = '/index.php';
$_SERVER['ORIG_SCRIPT_NAME'] = '/index.php';

require __DIR__.'/index.php';
