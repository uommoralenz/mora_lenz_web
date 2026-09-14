<?php

// Routing shim for /team — see ../shim.php for why this exists.
// Delete this folder once nginx can rewrite to index.php.

$path = '/team';

require __DIR__.'/../shim.php';
