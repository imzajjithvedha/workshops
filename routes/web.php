<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route(auth()->user()->role->dashboardRoute())
        : redirect()->route('login');
})->name('home');
