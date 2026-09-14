<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Where the application lives
|--------------------------------------------------------------------------
|
| By default the app sits one level above this file (the normal Laravel
| layout: /laravel/public/index.php -> /laravel).
|
| On CWP shared hosting you usually cannot point the document root at
| public/. In that case:
|
|   1. Upload the whole app to  /home/moralenz/moralenz_app
|   2. Copy the CONTENTS of public/ into  /home/moralenz/public_html
|   3. Change the line below to:  $appBase = __DIR__.'/../moralenz_app';
|
| Nothing else needs to change.
|
*/
$appBase = __DIR__.'/..';

// Maintenance mode, if `php artisan down` was ever run...
if (file_exists($maintenance = $appBase.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Composer autoloader...
require $appBase.'/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once $appBase.'/bootstrap/app.php';

$app->handleRequest(Request::capture());
