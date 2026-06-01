<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::get('/dashboard', DashboardController::class)
            ->middleware('permission:admin.dashboard.view')
            ->name('dashboard');

        Route::get('/users', [UserController::class, 'index'])->middleware('permission:admin.users.view')->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->middleware('permission:admin.users.create')->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->middleware('permission:admin.users.create')->name('users.store');
        Route::get('/users/{user}', [UserController::class, 'show'])->middleware('permission:admin.users.view')->name('users.show');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->middleware('permission:admin.users.update')->name('users.edit');
        Route::match(['put', 'patch'], '/users/{user}', [UserController::class, 'update'])->middleware('permission:admin.users.update')->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->middleware('permission:admin.users.delete')->name('users.destroy');

        Route::get('/categories', [CategoryController::class, 'index'])->middleware('permission:admin.categories.view')->name('categories.index');
        Route::get('/categories/create', [CategoryController::class, 'create'])->middleware('permission:admin.categories.create')->name('categories.create');
        Route::post('/categories', [CategoryController::class, 'store'])->middleware('permission:admin.categories.create')->name('categories.store');
        Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->middleware('permission:admin.categories.update')->name('categories.edit');
        Route::match(['put', 'patch'], '/categories/{category}', [CategoryController::class, 'update'])->middleware('permission:admin.categories.update')->name('categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->middleware('permission:admin.categories.delete')->name('categories.destroy');

        Route::get('/orders', [OrderController::class, 'index'])->middleware('permission:admin.orders.view')->name('orders.index');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->middleware('permission:admin.orders.view')->name('orders.show');
        Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->middleware('permission:admin.orders.update_status')->name('orders.update-status');
        Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->middleware('permission:admin.orders.delete')->name('orders.destroy');

        Route::get('/products', [ProductController::class, 'index'])->middleware('permission:admin.products.view')->name('products.index');
        Route::get('/products/create', [ProductController::class, 'create'])->middleware('permission:admin.products.create')->name('products.create');
        Route::post('/products', [ProductController::class, 'store'])->middleware('permission:admin.products.create')->name('products.store');
        Route::get('/products/{product}', [ProductController::class, 'show'])->middleware('permission:admin.products.view')->name('products.show');
        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->middleware('permission:admin.products.update')->name('products.edit');
        Route::match(['put', 'patch'], '/products/{product}', [ProductController::class, 'update'])->middleware('permission:admin.products.update')->name('products.update');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->middleware('permission:admin.products.delete')->name('products.destroy');

        Route::get('/roles', [RoleController::class, 'index'])->middleware('permission:admin.roles.view')->name('roles.index');
        Route::get('/roles/create', [RoleController::class, 'create'])->middleware('permission:admin.roles.create')->name('roles.create');
        Route::post('/roles', [RoleController::class, 'store'])->middleware('permission:admin.roles.create')->name('roles.store');
        Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->middleware('permission:admin.roles.update')->name('roles.edit');
        Route::match(['put', 'patch'], '/roles/{role}', [RoleController::class, 'update'])->middleware('permission:admin.roles.update')->name('roles.update');
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->middleware('permission:admin.roles.delete')->name('roles.destroy');
    });
