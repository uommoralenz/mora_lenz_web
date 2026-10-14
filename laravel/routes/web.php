<?php

use App\Http\Controllers\Public_\ContactController;
use App\Http\Controllers\Public_\EventController;
use App\Http\Controllers\Public_\HomeController;
use App\Http\Controllers\Public_\PageController;
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
Route::get('/events/preview/{token}', [EventController::class, 'preview'])
    ->where('token', '[A-Za-z0-9]{40}')
    ->name('events.preview');
Route::get('/events/{slug}', [EventController::class, 'show'])->name('events.show');

Route::get('/team', [TeamController::class, 'index'])->name('team');
Route::get('/team/{slug}', [TeamController::class, 'show'])
    ->where('slug', '[A-Za-z0-9_-]+')
    ->name('team.show');

Route::get('/about', [PageController::class, 'about'])->name('about');

// The gallery became the Past Events section; old links land there.
Route::get('/gallery', fn () => redirect()->to(\App\Support\Links::pastEvents(), 301))->name('gallery');

Route::get('/services/{type}', [ServiceController::class, 'show'])
    ->whereIn('type', ['photography', 'videography'])
    ->name('services.show');

Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
