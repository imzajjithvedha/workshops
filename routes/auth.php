<?php

use App\Http\Controllers\Auth\AuthenticationController;
use Illuminate\Support\Facades\Route;

Route::middleware('redirect_if_authenticated')->group(function () {
    Route::get('login', [AuthenticationController::class, 'login'])->name('login');
    Route::post('login', [AuthenticationController::class, 'store'])->name('login.store')->middleware('throttle:login');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticationController::class, 'logout'])->name('logout');
});
