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

    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // Rumah Kos CRUD
    Route::prefix('/rumah-kos')->group(function () {
        Route::get('/create', [RumahKosController::class, 'create'])->name('rumah-kos.create');
        Route::post('/store', [RumahKosController::class, 'store'])->name('rumah-kos.store');


        // ========== KAMAR ==========
        // LIST kamar
        Route::get('/{idKos}/kamar', [KamarController::class, 'index'])->name('kamar.index');

        // TAMBAH kamar
        Route::post('/{idKos}/kamar', [KamarController::class, 'store'])->name('kamar.store');

        // DETAIL kamar
        Route::get('/kamar/{idKamar}', [KamarController::class, 'show'])->name('kamar.show');

        // UPDATE kamar
        Route::put('/kamar/{idKamar}', [KamarController::class, 'update'])->name('kamar.update');

        // DELETE kamar
        Route::delete('/kamar/{idKamar}', [KamarController::class, 'destroy'])->name('kamar.destroy');

        // Detail rumah kos
        Route::get('/{id}/detail', [RumahKosController::class, 'detail'])
            ->name('rumah-kos.detail');
    });

    // Sidebar AJAX
    Route::get('/sidebar/kamar/{idKos}', [SidebarController::class, 'fetchKamar']);
    Route::get('/sidebar/pembayaran/{idKos}', [SidebarController::class, 'fetchPembayaran']);
});
