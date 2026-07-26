<?php

use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::middleware('guest:admin')->group(function () {
            Route::get('/', [LoginController::class, 'showForm'])->name('login');
            Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
        });
        Route::middleware('auth:admin')->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
            Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
        });
    });
