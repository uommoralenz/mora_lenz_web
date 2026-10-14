<?php

// Routing shim for /about — see ../shim.php for why this exists.
// Delete this folder once nginx can rewrite to index.php.

$path = '/about';

require __DIR__.'/../shim.php';
