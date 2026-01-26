<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\JadwalController;
use App\Http\Controllers\Api\PresensiController;
use App\Http\Controllers\Api\JurnalController;
use App\Http\Controllers\Api\IzinController;

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
    // GANTI MENJADI:
    Route::post('/mulai-kelas', [JadwalController::class, 'mulaiKelas']);
    
});
