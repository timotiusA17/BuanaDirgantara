<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\ProdukController;
use App\Models\Produk;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/guest-login', [AuthController::class, 'loginAsGuest'])->name('guest.login');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');

Route::middleware(['auth'])->group(function () {
    Route::get('/', [PelangganController::class, 'home'])->name('home');


    // Middleware untuk Admin
    Route::middleware(['auth', RoleMiddleware::class . ':admin'])->group(function () {
        Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
        Route::get('/admin/pembelian/history/{pelanggan_id}', [AdminController::class, 'getHistoryByPelanggan']);
        Route::post('/admin/pembelian/edit/{id}', [AdminController::class, 'editPembelian']);
        Route::delete('/admin/pembelian/delete/{id}', [AdminController::class, 'deletePembelian'])->name('admin.pembelian.delete');
        Route::post('/admin/tambah-pembelian/{id}', [AdminController::class, 'tambahPembelian'])->name('admin.tambahPembelian');
        Route::post('/admin/pelanggan/tambah-pembelian/{id}', [AdminController::class, 'tambahPembelian'])->name('admin.tambahPembelian');

        Route::view('/admin/katalog', 'admin.katalog')->name('admin.katalog');
        // Route::view('/admin/pembelian', 'admin.pembelian')->name('admin.pembelian');
        Route::view('/admin/leaderboard', 'admin.leaderboard')->name('admin.leaderboard');
        Route::view('/admin/promo', 'admin.promo')->name('admin.promo');
        Route::view('/admin/akun', 'admin.akun')->name('admin.akun');
        Route::post('/admin/tambah-pembelian/{id}', [AdminController::class, 'tambahPembelian'])->name('admin.tambahPembelian');
    });

    Route::middleware(['auth',  RoleMiddleware::class . ':admin'])->group(function () {
        Route::get('/admin/katalog', [ProdukController::class, 'index'])->name('admin.katalog');
        Route::post('/admin/katalog', [ProdukController::class, 'store'])->name('admin.katalog.store');
        Route::delete('/admin/katalog/{KODEB}', [ProdukController::class, 'deleteProduk'])->name('admin.katalog.delete');
        Route::put('/admin/katalog/{id}', [ProdukController::class, 'update'])->name('admin.katalog.update');
        Route::post('/admin/reward/update', [AdminController::class, 'updateRewardImage'])->name('admin.reward.update');
        Route::get('/admin/pembelian', [AdminController::class, 'managePembelian'])->name('admin.pembelian');
        Route::post('/admin/update-achievement', [AdminController::class, 'updatePembelian'])->name('admin.update-pembelian');
        Route::get('/admin/promo', [AdminController::class, 'promoPage'])->name('admin.promo');
        Route::post('/admin/promo/store', [AdminController::class, 'storePromo'])->name('admin.promo.store');
        Route::get('/admin/promo/{id}/edit', [AdminController::class, 'edit'])->name('admin.promo.edit');
        Route::put('/admin/promo/{id}', [AdminController::class, 'update'])->name('admin.promo.update');
        Route::delete('/admin/promo/{id}', [AdminController::class, 'destroy'])->name('admin.promo.destroy');
    });

    // Middleware untuk Owner
    Route::middleware(['role:owner'])->group(function () {
        Route::get('/owner/dashboard', function () {
            return view('owner.dashboard');
        })->name('owner.dashboard');
    });

    // Middleware untuk Pelanggan
    Route::middleware(['auth', RoleMiddleware::class . ':pelanggan'])->group(function () {
        Route::get('/leaderboard', [PelangganController::class, 'leaderboard'])->name('leaderboard');
        Route::view('/promo', 'pelanggan.promo')->name('promo');
        Route::get('/profile', [PelangganController::class, 'profile'])->name('pelanggan.profile');
        Route::post('/profile/update-password', [PelangganController::class, 'updatePassword'])->name('pelanggan.updatePassword');
        Route::post('/update-username', [PelangganController::class, 'updateUsername'])->name('pelanggan.updateUsername');
        Route::get('/personal-achievement', [PelangganController::class, 'personalAchievement'])->name('personal.achievement');
        Route::get('/promo', [PelangganController::class, 'promoPage'])->name('promo');
    });

    // admin menambahkan total pembelian
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::post('/admin/pembelian/{id}', [AdminController::class, 'tambahPembelian'])->name('admin.tambahPembelian');

    // admin menambahkan user baru
    Route::get('/admin/akun', [AdminController::class, 'showCreatePelangganForm'])->name('admin.akun');
    Route::post('/admin/akun', [AdminController::class, 'storePelanggan'])->name('admin.akun.store');

    //admin delete pelanggan
    Route::delete('/admin/akun/{id}', [AdminController::class, 'deleteUser'])->name('admin.akun.delete');

    //admin edit pelanggan
    Route::get('/admin/akun/{id}/edit', [AdminController::class, 'editPelanggan'])->name('admin.akun.edit');
    Route::post('/admin/akun/{id}/update', [AdminController::class, 'updatePelanggan'])->name('admin.akun.update');

    Route::post('/logout', function () {
        Auth::logout();
        return redirect('/'); // atau redirect ke halaman login
    })->name('logout');
});
