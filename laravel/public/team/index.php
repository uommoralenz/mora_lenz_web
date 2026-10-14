<?php

// Routing shim for /team — see ../shim.php for why this exists.
// Delete this folder once nginx can rewrite to index.php.
//
// ?t=<slug> shows one year's team: there is a page per year, and they cannot
// each have their own folder.

$path = '/team';

if (isset($_GET['t']) && is_string($_GET['t'])) {
    $slug = trim($_GET['t'], '/');

    // Term slugs are alpha_dash only; anything else is ignored rather than passed on.
    if ($slug !== '' && preg_match('/^[A-Za-z0-9_-]{1,100}$/', $slug)) {
        $path .= '/'.$slug;
    }

    unset($_GET['t']);
}

require __DIR__.'/../shim.php';
