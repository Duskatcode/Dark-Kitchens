<?php

use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware(['auth', EnsureUserIsAdmin::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::resource('users', AdminUserController::class);
    });

require __DIR__.'/auth.php';

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class)
            ->except(['show']);
    });

Route::middleware(['auth', 'role:client'])
    ->prefix('client')
    ->name('client.')
    ->group(function (): void {
        // Persona 2: categories, products, menu
    });

Route::middleware(['auth', 'role:cook'])
    ->prefix('cook')
    ->name('cook.')
    ->group(function (): void {
        // Persona 3: pending orders
    });
