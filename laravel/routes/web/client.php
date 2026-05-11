<?php

use App\Http\Controllers\Client\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:client'])
    ->prefix('client')
    ->name('client.')
    ->group(function (): void {
        Route::get('/dashboard', DashboardController::class)->name('dashboard');
    });
