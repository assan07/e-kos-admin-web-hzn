<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\AdminAuth;
use App\Http\Controllers\RumahKosController;
use App\Http\Controllers\SidebarController;
use App\Http\Controllers\KamarController;

// Login
Route::get('/', [AuthController::class, 'loginPage'])->name('login');
Route::post('/login/process', [AuthController::class, 'login'])->name('login.process');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware([AdminAuth::class])->group(function () {
    // Sidebar AJAX
    Route::get('/sidebar/kamar/{idDoc}', [SidebarController::class, 'fetchKamar']);
    Route::get('/sidebar/pembayaran/{idDoc}', [SidebarController::class, 'fetchPembayaran']);

    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    Route::prefix('/rumah-kos')->group(function () {
        Route::get('/create', [RumahKosController::class, 'create'])->name('rumah-kos.create');
        Route::post('/store', [RumahKosController::class, 'store'])->name('rumah-kos.store');

        // ===== KAMAR =====
        Route::get('/{idDoc}/kamar', [KamarController::class, 'index'])->name('kamar.index');
        Route::post('/{idDoc}/kamar', [KamarController::class, 'store'])->name('kamar.store');

        Route::prefix('/{idDoc}/kamar')->group(function () {
            Route::get('{idKamar}/detail', [KamarController::class, 'showDetail'])->name('kamar.detail');
            Route::put('{idKamar}/update', [KamarController::class, 'update'])->name('kamar.update');
            Route::delete('{idKamar}', [KamarController::class, 'destroy'])
                ->name('admin.kamar.destroy');
        });

        // ===== Detail Rumah Kos (WAJIB DITARUH PALING BAWAH) =====
        Route::get('/{id}/detail', [RumahKosController::class, 'detail'])
            ->name('rumah-kos.detail');
    });
});
