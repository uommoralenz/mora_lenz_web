<?php

use App\Http\Controllers\AdminUploadController;
use Illuminate\Support\Facades\Route;

Route::post('/admin/uploads', [AdminUploadController::class, 'store'])->middleware('throttle:10,1')->name('api.admin.uploads.store');
