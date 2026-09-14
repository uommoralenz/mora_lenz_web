<?php

use App\Http\Controllers\SetupController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| One-time installer
|--------------------------------------------------------------------------
|
| Registered with no middleware at all (see bootstrap/app.php) so it works on a
| brand new install, before APP_KEY exists and before sessions can be encrypted.
|
| It answers 404 unless SETUP_KEY is set in .env and matches exactly. Blank out
| SETUP_KEY as soon as the install finishes.
|
*/

Route::get('/setup/{key}', [SetupController::class, 'run'])
    ->where('key', '[A-Za-z0-9_\-]{16,128}')
    ->name('setup');
