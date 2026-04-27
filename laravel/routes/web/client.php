<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:client'])
    ->prefix('client')
    ->name('client.')
    ->group(function (): void {
        // Persona 2: categories, products, menu
    });
