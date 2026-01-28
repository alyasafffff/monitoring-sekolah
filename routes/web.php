<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthWebController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SiswaWebController;
use App\Http\Controllers\KelasWebController;
use App\Http\Controllers\UserWebController;
use App\Http\Controllers\MapelWebController;
use App\Http\Controllers\JadwalWebController;

// 1. HALAMAN LOGIN (Tamu)
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthWebController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthWebController::class, 'login'])->name('login.post');
});

// 2. HALAMAN SETELAH LOGIN
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [AuthWebController::class, 'logout'])->name('logout');

    // Dashboard Utama (Multi Role)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // --- GROUP KHUSUS ADMIN ---
    // Semua yang ada di dalam sini otomatis punya awalan '/admin'
    // Dan HANYA bisa diakses oleh role 'admin'
    Route::middleware(['role:admin'])->prefix('admin')->group(function () {

        // A. Manajemen Siswa
        // URL: /admin/siswa
        Route::resource('siswa', SiswaWebController::class);
        // URL: /admin/siswa/{id}/cetak
        Route::get('siswa/{id}/cetak', [SiswaWebController::class, 'cetakKartu'])->name('siswa.cetak');

        // B. Manajemen Kelas (SUDAH DIPINDAH KE DALAM SINI)
        // URL: /admin/kelas
        Route::resource('kelas', KelasWebController::class);

        Route::resource('users', UserWebController::class);
        Route::resource('mapel', MapelWebController::class);
        Route::resource('jadwal', JadwalWebController::class);
        // Nanti tambah Mapel & Guru disini juga...
    });

    // --- GROUP KHUSUS BK ---
    Route::middleware('role:bk')->prefix('bk')->group(function () {
        Route::get('/pelanggaran', function () {
            return "Halaman Monitoring Pelanggaran";
        });
    });
});

// JANGAN MENULIS RUTE APAPUN DI BAWAH SINI!
// KARENA AKAN JADI PUBLIC (BISA DIAKSES TANPA LOGIN)