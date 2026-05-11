<?php

use App\Http\Controllers\DashboardRedirectController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home.index');

Route::get('/dashboard', DashboardRedirectController::class)
    ->middleware(['auth'])
    ->name('dashboard');
