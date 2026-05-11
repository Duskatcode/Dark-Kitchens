<?php

use App\Http\Controllers\Client\DashboardController;
use App\Http\Controllers\Client\MenuController;
use App\Http\Controllers\Client\OrderController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:client'])
    ->prefix('client')
    ->name('client.')
    ->group(function (): void {
        Route::get('/dashboard', DashboardController::class)->name('dashboard');

        Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
        Route::get('/menu/{product}', [MenuController::class, 'show'])->name('menu.show');

        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    });
