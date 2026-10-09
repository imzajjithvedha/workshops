<?php

use App\Enums\UserRole;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:' . UserRole::ADMIN->value])->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.users.index');
    });

    // User routes
    Route::controller(UserController::class)
        ->prefix('users')
        ->name('users.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('{user}', 'edit')->name('edit');
            Route::patch('{user}', 'update')->name('update');
        });
    // User routes

    // Activity log routes
    Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    // Activity log routes
});
