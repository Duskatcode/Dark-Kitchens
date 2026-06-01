<?php

use App\Http\Controllers\Cook\DashboardController;
use App\Http\Controllers\Cook\OrderController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:cook'])
    ->prefix('cook')
    ->name('cook.')
    ->group(function (): void {
        Route::get('/dashboard', DashboardController::class)->name('dashboard');

        Route::get('/orders', [OrderController::class, 'index'])->middleware('permission:cook.orders.view')->name('orders.index');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->middleware('permission:cook.orders.view')->name('orders.show');
        Route::patch('/orders/{order}/start', [OrderController::class, 'start'])->middleware('permission:cook.orders.update_status')->name('orders.start');
        Route::patch('/orders/{order}/complete', [OrderController::class, 'complete'])->middleware('permission:cook.orders.update_status')->name('orders.complete');
    });
