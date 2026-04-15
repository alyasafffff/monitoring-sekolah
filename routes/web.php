<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthWebController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SiswaWebController;
use App\Http\Controllers\KelasWebController;
use App\Http\Controllers\UserWebController;
use App\Http\Controllers\MapelWebController;
use App\Http\Controllers\JadwalWebController;
use App\Http\Controllers\JamPelajaranConfigController;
use App\Http\Controllers\KegiatanWebController;
use App\Http\Controllers\KepsekController;
use App\Http\Controllers\RekapWebController;
use App\Http\Controllers\LaporanBkController;
use App\Http\Controllers\SiswaBkController;

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
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update-all', [ProfileController::class, 'updateAll'])->name('profile.update.all');

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
        Route::resource('kegiatan', KegiatanWebController::class);
        Route::get('/admin/jadwal/create', [JadwalWebController::class, 'create'])->name('jadwal.create');
        Route::get('/admin/jadwal/export', [JadwalWebController::class, 'exportPdf'])->name('jadwal.export');
        Route::resource('users', UserWebController::class);
        Route::resource('mapel', MapelWebController::class);
        Route::resource('jadwal', JadwalWebController::class);
        Route::resource('jam-config', JamPelajaranConfigController::class)->except(['create', 'show', 'edit', 'update']);
        Route::get('/admin/rekap-presensi', [RekapWebController::class, 'index'])->name('rekap.index');
        Route::get('/admin/rekap/export', [RekapWebController::class, 'export'])->name('rekap.export');
    });

    // --- GROUP KHUSUS BK ---
    Route::middleware('role:bk')->prefix('bk')->group(function () {
        Route::get('/bk/laporan-alpha', [LaporanBkController::class, 'index'])->name('bk.laporan.alpha');
        Route::get('/bk/siswa', [App\Http\Controllers\SiswaBkController::class, 'index'])->name('bk.siswa.index');
        Route::get('/bk/siswa/{id}', [App\Http\Controllers\SiswaBkController::class, 'show'])->name('bk.siswa.show');
        Route::get('/bk/laporan/export', [SiswaBkController::class, 'export'])->name('bk.laporan.export');
    });

    Route::middleware(['auth', 'role:kepsek'])->prefix('kepsek')->name('kepsek.')->group(function () {
    Route::get('/monitoring-jurnal', [KepsekController::class, 'monitoringJurnal'])->name('monitoring.jurnal');
    Route::get('/monitoring-presensi', [KepsekController::class, 'monitoringPresensi'])->name('monitoring.presensi');
});
});

// JANGAN MENULIS RUTE APAPUN DI BAWAH SINI!
// KARENA AKAN JADI PUBLIC (BISA DIAKSES TANPA LOGIN)