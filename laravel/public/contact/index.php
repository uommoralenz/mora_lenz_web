<?php

// Routing shim for the contact form POST — see ../shim.php.
//
// The form posts to /contact/ WITH the trailing slash on purpose: nginx would
// answer /contact with a 301 to /contact/, and a browser turns a redirected
// POST into a GET, silently losing the message.
//
// Delete this folder once nginx can rewrite to index.php.

$path = '/contact';

require __DIR__.'/../shim.php';
