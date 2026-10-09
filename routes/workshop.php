<?php

use App\Enums\UserRole;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\WorkshopController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:' . UserRole::MANAGER->value])
    ->controller(WorkshopController::class)
    ->prefix('workshops')
    ->name('workshops.')
    ->group(function () {
        Route::get('create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('{workshop}/edit', 'edit')->name('edit');
        Route::patch('{workshop}', 'update')->name('update');
    });
// Workshop management routes

// Shared by managers and staff.
Route::middleware(['auth', 'role:' . UserRole::MANAGER->value . ',' . UserRole::STAFF->value])->group(function () {
    // Workshop routes
    Route::controller(WorkshopController::class)
        ->prefix('workshops')
        ->name('workshops.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('{workshop}', 'show')->name('show');
        });
    // Workshop routes

    // Registration routes
    Route::controller(RegistrationController::class)
        ->prefix('workshops/{workshop}/registrations')
        ->name('registrations.')
        ->scopeBindings()
        ->group(function () {
            Route::post('/', 'store')->name('store');
            Route::patch('{registration}/cancel', 'cancel')->name('cancel');
        });
    // Registration routes
});
