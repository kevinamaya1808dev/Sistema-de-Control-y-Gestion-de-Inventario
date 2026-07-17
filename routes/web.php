<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MovementController;

// Rutas de invitados
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
});

// Rutas autenticadas
Route::middleware('auth')->group(function () {
    
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/movimientos', [MovementController::class, 'index'])->name('movements.index');
    Route::post('/movimientos', [MovementController::class, 'store'])->name('movements.store')->middleware('permission:register-movements');

    // --- Permisos de Categorías ---
    Route::middleware('permission:manage-categories')->group(function () {
        Route::get('/categorias', [CategoryController::class, 'index'])->name('categories.index');
        Route::post('/categorias', [CategoryController::class, 'store'])->name('categories.store');
        Route::put('/categorias/{category}', [CategoryController::class, 'update'])->name('categories.update');
      
    });

    // --- Permisos de Productos ---
    Route::middleware('permission:manage-products')->group(function () {
        Route::get('/productos', [ProductController::class, 'index'])->name('products.index');
        Route::post('/productos', [ProductController::class, 'store'])->name('products.store');
        Route::put('/productos/{product}', [ProductController::class, 'update'])->name('products.update');
    
    });

    // --- Permiso Especial: Eliminar Catálogo ---
    Route::middleware('permission:delete-catalog')->group(function () {
        Route::delete('/categorias/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
        Route::delete('/productos/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    });

    // --- Permiso de Administración (Gestión de Usuarios) ---
    Route::middleware('permission:manage-users')->group(function () {
        Route::get('/usuarios', [UserController::class, 'index'])->name('users.index');
        Route::post('/usuarios', [UserController::class, 'store'])->name('users.store');
        Route::put('/usuarios/{user}', [UserController::class, 'update'])->name('users.update');
        
    });
    });
