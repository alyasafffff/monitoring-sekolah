<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\IzinController;
use App\Http\Controllers\Api\JadwalController;
use App\Http\Controllers\Api\JurnalController;
// Controller PresensiController & IzinController bisa ditambah nanti jika fiturnya sudah dibuat

/*
|--------------------------------------------------------------------------
| API Routes (KHUSUS MOBILE APP GURU)
|--------------------------------------------------------------------------
|
| Route ini khusus melayani aplikasi Flutter untuk Guru.
| Fitur CRUD Admin (Siswa, Kelas, User, dll) sudah dihapus karena
| Admin mengelolanya lewat Web Dashboard, bukan lewat HP.
|
*/

// ============================================================================
// 1. PUBLIC ROUTES (Bisa diakses tanpa Login)
// ============================================================================

// Login Guru (Untuk dapat Token)
Route::post('/login', [AuthController::class, 'login']);


// ============================================================================
// 2. PROTECTED ROUTES (Harus Login & Punya Token)
// ============================================================================
Route::middleware('auth:sanctum')->group(function () {

    // --- A. AUTH & PROFILE ---

    // Cek Profile Guru yang sedang login
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Logout (Hapus Token di HP)
    Route::post('/logout', [AuthController::class, 'logout']);


    // --- B. FITUR UTAMA: JADWAL & MENGAJAR ---

    // 1. Halaman Home: Lihat Jadwal Mengajar Hari Ini
    // Output: List jadwal (Jam, Kelas, Mapel)
    // Di file routes/api.php
    Route::get('/jadwal-hari-ini', [JadwalController::class, 'index']); // <--- Panggil 'index'
    Route::post('/mulai-kelas', [JadwalController::class, 'mulaiKelas']); // <--- Tambah ini
    Route::get('/walikelas/siswa', [IzinController::class, 'getSiswa']);
    Route::post('/walikelas/izin', [IzinController::class, 'inputIzin']);
    Route::get('/jurnal/{jurnal_id}/presensi', [JurnalController::class, 'getPresensiSiswa']);
    Route::post('/jurnal/{jurnal_id}/update', [JurnalController::class, 'updateJurnal']);

    // 3. Halaman Absensi: Ambil Daftar Siswa untuk Diabsen
    // Input: ID Jurnal (didapat dari respon 'mulai-kelas')
    // Output: List Nama Siswa & Status Kehadiran (Default: Hadir)
    Route::get('/jurnal/{jurnal_id}/presensi', [JurnalController::class, 'getPresensiSiswa']);

    // 4. Klik "Simpan": Update Status Kehadiran & Simpan Materi Jurnal
    // Input: Materi, Status Guru, List Absen Siswa (Array)
    Route::post('/jurnal/{jurnal_id}/update', [JurnalController::class, 'updateJurnal']);
    Route::get('/riwayat-mengajar', [JurnalController::class, 'getRiwayat']);


    // --- C. FITUR TAMBAHAN (WALI KELAS) ---
    // (Aktifkan jika controller-nya sudah siap nanti)

    // Route::post('/input-izin', [IzinController::class, 'inputIzin']); 
    // Route::get('/laporan-kelas', [LaporanController::class, 'index']);

});
