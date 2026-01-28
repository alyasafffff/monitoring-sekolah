<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\JadwalController;
use App\Http\Controllers\Api\PresensiController;
use App\Http\Controllers\Api\JurnalController;
use App\Http\Controllers\Api\IzinController;
use App\Http\Controllers\Api\MataPelajaranController;



// Jalur Login (Tidak perlu token)
Route::post('/login', [AuthController::class, 'login']);

// Jalur yang butuh Token (Harus login dulu)
Route::middleware('auth:sanctum')->group(function () {

    // API Cek Data Diri Guru
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // API Lihat Jadwal Mengajar Hari Ini
    Route::get('/jadwal-hari-ini', [JadwalController::class, 'jadwalHariIni']); // <--- Rute Jadwal
    // API Scan QR Code
    Route::post('/scan-qr', [PresensiController::class, 'scanQr']);
    // API Simpan Jurnal & Deteksi Alpha
    Route::post('/simpan-jurnal', [JurnalController::class, 'simpanJurnal']);
    // API Input Izin oleh Wali Kelas
    Route::post('/input-izin', [IzinController::class, 'inputIzin']);
    // mulaiKelas:
    Route::post('/mulai-kelas', [JadwalController::class, 'mulaiKelas']);

    // ADMIN - CRUD SISWA
    Route::get('/admin/siswa', [App\Http\Controllers\Api\SiswaController::class, 'index']);
    Route::post('/admin/siswa', [App\Http\Controllers\Api\SiswaController::class, 'store']);
    Route::put('/admin/siswa/{id}', [App\Http\Controllers\Api\SiswaController::class, 'update']);
    Route::delete('/admin/siswa/{id}', [App\Http\Controllers\Api\SiswaController::class, 'destroy']);

    // ADMIN - CRUD USER (Admin, Guru, BK, Kepsek)
    Route::get('/admin/users', [App\Http\Controllers\Api\UserController::class, 'index']);
    Route::post('/admin/users', [App\Http\Controllers\Api\UserController::class, 'store']);
    Route::put('/admin/users/{id}', [App\Http\Controllers\Api\UserController::class, 'update']);
    Route::delete('/admin/users/{id}', [App\Http\Controllers\Api\UserController::class, 'destroy']);

    // ADMIN - CRUD KELAS
    Route::get('/admin/kelas', [App\Http\Controllers\Api\KelasController::class, 'index']);
    Route::get('/admin/kelas/{id}', [App\Http\Controllers\Api\KelasController::class, 'show']);
    Route::post('/admin/kelas', [App\Http\Controllers\Api\KelasController::class, 'store']);
    Route::put('/admin/kelas/{id}', [App\Http\Controllers\Api\KelasController::class, 'update']);
    Route::delete('/admin/kelas/{id}', [App\Http\Controllers\Api\KelasController::class, 'destroy']);



    // ADMIN - CRUD MATA PELAJARAN
    Route::get('/admin/mapel', [MataPelajaranController::class, 'index']);
    Route::get('/admin/mapel/{id}', [MataPelajaranController::class, 'show']);
    Route::post('/admin/mapel', [MataPelajaranController::class, 'store']);
    Route::put('/admin/mapel/{id}', [MataPelajaranController::class, 'update']);
    Route::delete('/admin/mapel/{id}', [MataPelajaranController::class, 'destroy']);

    // ADMIN - CRUD JADWAL PELAJARAN
    Route::get('/admin/jadwal', [App\Http\Controllers\Api\JadwalPelajaranController::class, 'index']);
    Route::get('/admin/jadwal/{id}', [App\Http\Controllers\Api\JadwalPelajaranController::class, 'show']);
    Route::post('/admin/jadwal', [App\Http\Controllers\Api\JadwalPelajaranController::class, 'store']);
    Route::put('/admin/jadwal/{id}', [App\Http\Controllers\Api\JadwalPelajaranController::class, 'update']);
    Route::delete('/admin/jadwal/{id}', [App\Http\Controllers\Api\JadwalPelajaranController::class, 'destroy']);

    // --- JURNAL / ABSENSI ---
    Route::get('/admin/jurnal', [App\Http\Controllers\Api\JurnalController::class, 'index']);
    Route::post('/admin/jurnal', [App\Http\Controllers\Api\JurnalController::class, 'store']);
});

