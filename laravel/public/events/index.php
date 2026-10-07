<?php

/*
|--------------------------------------------------------------------------
| Routing shim — /events
|--------------------------------------------------------------------------
|
| This server runs nginx with no rewrite rule, so a request for /events never
| reaches the front controller. Because this folder physically exists, nginx
| serves THIS file instead, and we hand off to the real index.php with the URL
| Laravel is expecting.
|
| ?e=<slug> shows a single event, because event slugs are unlimited and cannot
| each have their own folder.
|
| DELETE THIS WHOLE FOLDER once nginx gets:
|     location / { try_files $uri $uri/ /index.php?$query_string; }
|
*/

$path = '/events';

if (isset($_GET['e']) && is_string($_GET['e'])) {
    $slug = trim($_GET['e'], '/');

    // Slugs are alpha_dash only; anything else is ignored rather than passed on.
    if ($slug !== '' && preg_match('/^[A-Za-z0-9_-]{1,200}$/', $slug)) {
        $path .= '/'.$slug;
    }

    unset($_GET['e']);
}

if (isset($_GET['preview']) && is_string($_GET['preview'])) {
    $token = $_GET['preview'];

    // Preview tokens are exactly 40 alphanumeric characters.
    if (preg_match('/^[A-Za-z0-9]{40}$/', $token)) {
        $path = '/events/preview/'.$token;
    }

    unset($_GET['preview']);
}

require __DIR__.'/../shim.php';
