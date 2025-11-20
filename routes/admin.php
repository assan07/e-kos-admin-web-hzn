<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\AdminAuth;
use App\Http\Controllers\RumahKosController;
use App\Http\Controllers\SidebarController;
use App\Http\Controllers\KamarController;
use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\DashboardController;

// Login
Route::get('/', [AuthController::class, 'loginPage'])->name('login');
Route::post('/login/process', [AuthController::class, 'login'])->name('login.process');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware([AdminAuth::class])->group(function () {
    // Sidebar AJAX
    Route::get('/sidebar/kamar/{idDoc}', [SidebarController::class, 'fetchKamar']);
    Route::get('/sidebar/pembayaran/{idDoc}', [SidebarController::class, 'fetchPembayaran']);

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/search', [DashboardController::class, 'search'])->name('dashboard.search');

    Route::prefix('/rumah-kos')->group(function () {
        Route::get('/create', [RumahKosController::class, 'create'])->name('rumah-kos.create');
        Route::post('/store', [RumahKosController::class, 'store'])->name('rumah-kos.store');
        Route::get('/data', [RumahKosController::class, 'index'])->name('rumah_kos.index');
        // Edit & Update Rumah Kos
        Route::get('{id}/edit', [RumahKosController::class, 'edit'])->name('rumah_kos.edit');
        Route::put('update/{id}', [RumahKosController::class, 'update'])->name('rumah_kos.update');

        Route::delete('/delete/{id}', [RumahKosController::class, 'destroy'])->name('rumah_kos.destroy');

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

    Route::prefix('/pesanan')->group(function () {
        Route::get('data-pesanan', [PesananController::class, 'index'])->name('admin.pesanan.index');
        Route::get('{idDoc}', [PesananController::class, 'detail'])->name('admin.pesanan.detail');
        Route::put('update/{idDoc}', [PesananController::class, 'update'])->name('admin.pesanan.update');
        Route::delete('{idDoc}', [PesananController::class, 'delete'])->name('admin.pesanan.delete');
    });
    Route::prefix('/pembayaran')->group(function () {
        Route::get('data-pembayaran', [PaymentsController::class, 'index'])->name('admin.pembayaran.index');
        Route::get('detail/{idDoc}', [PaymentsController::class, 'detail'])->name('admin.pembayaran.detail');
        Route::put('update/{idDoc}', [PaymentsController::class, 'update'])->name('admin.pembayaran.update');
        // Form tambah pembayaran
        Route::get('/add', [PaymentsController::class, 'addPaymentForm'])
            ->name('admin.pembayaran.addForm');

        // Proses tambah pembayaran
        Route::post('/add', [PaymentsController::class, 'addPayment'])
            ->name('admin.pembayaran.add');
        Route::delete('delete/{idDoc}', [PaymentsController::class, 'delete'])->name('admin.pembayaran.delete');
        Route::get('download/{kos}', [PaymentsController::class, 'download'])->name('admin.pembayaran.download');
    });
});
