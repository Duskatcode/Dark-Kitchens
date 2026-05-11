<?php

use App\Http\Controllers\Cook\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:cook'])
    ->prefix('cook')
    ->name('cook.')
    ->group(function (): void {
        Route::get('/dashboard', DashboardController::class)->name('dashboard');
    });
