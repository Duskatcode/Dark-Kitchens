<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:cook'])
    ->prefix('cook')
    ->name('cook.')
    ->group(function (): void {
        // Persona 3: pending orders
    });
