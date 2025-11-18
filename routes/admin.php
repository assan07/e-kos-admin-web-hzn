<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\AdminAuth;
use App\Http\Controllers\RumahKosController;

// Login
Route::get('/', [AuthController::class, 'loginPage'])->name('login');
Route::post('/login/process', [AuthController::class, 'login'])->name('login.process');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected dashboard
Route::middleware([AdminAuth::class])->group(function () {

    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // Rumah Kos CRUD
    Route::prefix('admin/rumah-kos')->group(function () {
        Route::get('/create', [RumahKosController::class, 'create'])->name('rumah-kos.create');
        Route::post('/store', [RumahKosController::class, 'store'])->name('rumah-kos.store');
    });
});
