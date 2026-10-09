<?php

use App\Enums\UserRole;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\RegistrationController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\WorkshopController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->name('login')->middleware('throttle:login');

    Route::middleware('auth:sanctum')->group(function () {
        // Auth routes
        Route::get('me', [AuthController::class, 'me'])->name('me');
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
        // Auth routes

        // User routes (admin)
        Route::middleware('role:' . UserRole::ADMIN->value)
            ->controller(UserController::class)
            ->prefix('users')
            ->name('users.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/', 'store')->name('store');
                Route::get('{user}', 'show')->name('show');
                Route::patch('{user}', 'update')->name('update');
            });
        // User routes

        // Workshop management routes (manager)
        Route::middleware('role:' . UserRole::MANAGER->value)
            ->controller(WorkshopController::class)
            ->prefix('workshops')
            ->name('workshops.')
            ->group(function () {
                Route::post('/', 'store')->name('store');
                Route::patch('{workshop}', 'update')->name('update');
            });
        // Workshop management routes

        Route::middleware('role:' . UserRole::MANAGER->value . ',' . UserRole::STAFF->value)->group(function () {
            // Workshop routes
            Route::controller(WorkshopController::class)
                ->prefix('workshops')
                ->name('workshops.')
                ->group(function () {
                    Route::get('/', 'index')->name('index');
                    Route::get('{workshop}', 'show')->name('show');
                    Route::get('{workshop}/history', 'history')->name('history');
                });
            // Workshop routes

            // Registration routes
            Route::controller(RegistrationController::class)->group(function () {
                Route::get('workshops/{workshop}/registrations', 'index')->name('registrations.index');
                Route::post('workshops/{workshop}/registrations', 'store')->name('registrations.store');
                Route::patch('registrations/{registration}/cancel', 'cancel')->name('registrations.cancel');
            });
            // Registration routes
        });
    });
});
