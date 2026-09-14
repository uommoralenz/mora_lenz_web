<?php

use App\Http\Controllers\Public_\ContactController;
use App\Http\Controllers\Public_\EventController;
use App\Http\Controllers\Public_\HomeController;
use App\Http\Controllers\Public_\ServiceController;
use App\Http\Controllers\Public_\TeamController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public website
|--------------------------------------------------------------------------
|
| These are the only routes served to visitors. There is no login page and no
| /admin route here on purpose: the admin panel is a separate application on
| Vercel and talks to this server only through /api/admin/*.
|
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{slug}', [EventController::class, 'show'])->name('events.show');

Route::get('/team', [TeamController::class, 'index'])->name('team');

Route::get('/gallery', [\App\Http\Controllers\Public_\GalleryController::class, 'index'])->name('gallery');

Route::get('/services/{type}', [ServiceController::class, 'show'])
    ->whereIn('type', ['photography', 'videography'])
    ->name('services.show');

Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
