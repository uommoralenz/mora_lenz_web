<?php

use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\MessageController;
use App\Http\Controllers\Admin\ServiceImageController;
use App\Http\Controllers\Admin\ServicePackageController;
use App\Http\Controllers\Admin\TeamController;
use App\Http\Controllers\Admin\UploadController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin API
|--------------------------------------------------------------------------
|
| Consumed only by the admin panel hosted on Vercel. Authentication is a bearer
| token issued by /api/admin/auth/login; CORS is limited to the origins listed
| in ADMIN_PANEL_ORIGINS. Nothing here is reachable from the public site.
|
*/

Route::prefix('admin')->group(function () {

    // --- Public (no token needed) ---
    Route::post('auth/login', [AuthController::class, 'login'])
        ->middleware('throttle:20,1');

    // --- Signed in ---
    Route::middleware('admin.auth')->group(function () {

        Route::get('auth/me', [AuthController::class, 'me']);
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::post('auth/password', [AuthController::class, 'changePassword']);

        Route::get('stats', [DashboardController::class, 'stats']);

        Route::post('uploads', [UploadController::class, 'store']);
        Route::delete('uploads', [UploadController::class, 'destroy']);

        // Events
        Route::post('events/reorder', [EventController::class, 'reorder']);
        Route::apiResource('events', EventController::class)
            ->scoped(['event' => 'id']);

        // Featured galleries (homepage showcase)
        Route::post('galleries/reorder', [GalleryController::class, 'reorder']);
        Route::apiResource('galleries', GalleryController::class)
            ->parameters(['galleries' => 'gallery'])
            ->except('show');

        // Service packages
        Route::post('service-packages/reorder', [ServicePackageController::class, 'reorder']);
        Route::apiResource('service-packages', ServicePackageController::class)
            ->parameters(['service-packages' => 'package'])
            ->except('show');

        // Service carousel images
        Route::post('service-images/reorder', [ServiceImageController::class, 'reorder']);
        Route::apiResource('service-images', ServiceImageController::class)
            ->parameters(['service-images' => 'serviceImage'])
            ->except('show');

        // Team hierarchy
        Route::get('team', [TeamController::class, 'tree']);

        Route::post('team/groups', [TeamController::class, 'storeGroup']);
        Route::post('team/groups/reorder', [TeamController::class, 'reorderGroups']);
        Route::match(['put', 'patch'], 'team/groups/{group}', [TeamController::class, 'updateGroup']);
        Route::delete('team/groups/{group}', [TeamController::class, 'destroyGroup']);

        Route::post('team/subgroups', [TeamController::class, 'storeSubgroup']);
        Route::post('team/subgroups/reorder', [TeamController::class, 'reorderSubgroups']);
        Route::match(['put', 'patch'], 'team/subgroups/{subgroup}', [TeamController::class, 'updateSubgroup']);
        Route::delete('team/subgroups/{subgroup}', [TeamController::class, 'destroySubgroup']);

        Route::post('team/members', [TeamController::class, 'storeMember']);
        Route::post('team/members/reorder', [TeamController::class, 'reorderMembers']);
        Route::match(['put', 'patch'], 'team/members/{member}', [TeamController::class, 'updateMember']);
        Route::delete('team/members/{member}', [TeamController::class, 'destroyMember']);

        // Contact inbox
        Route::apiResource('messages', MessageController::class)
            ->parameters(['messages' => 'message'])
            ->only(['index', 'update', 'destroy']);

        // --- Super admin only ---
        Route::middleware('admin.super')->group(function () {
            Route::apiResource('admins', AdminUserController::class)
                ->parameters(['admins' => 'admin'])
                ->except('show');
        });
    });
});
