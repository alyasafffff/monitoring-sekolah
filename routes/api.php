<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\IzinController;
use App\Http\Controllers\Api\JadwalController;
use App\Http\Controllers\Api\JurnalController;
use App\Http\Controllers\Api\ProfileController;

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

    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/jadwal-hari-ini', [JadwalController::class, 'index']); // <--- Panggil 'index'
    Route::post('/mulai-kelas', [JadwalController::class, 'mulaiKelas']); // <--- Tambah ini
    Route::get('/walikelas/siswa', [IzinController::class, 'getSiswa']);
    Route::post('/walikelas/izin', [IzinController::class, 'inputIzin']);
    Route::get('/jurnal/{jurnal_id}/presensi', [JurnalController::class, 'getPresensiSiswa']);
    Route::post('/jurnal/{jurnal_id}/update', [JurnalController::class, 'updateJurnal']);
    Route::get('/jurnal/{jurnal_id}/presensi', [JurnalController::class, 'getPresensiSiswa']);
    Route::post('/jurnal/{jurnal_id}/update', [JurnalController::class, 'updateJurnal']);
    Route::get('/riwayat-mengajar', [JurnalController::class, 'getRiwayat']);
    Route::get('/profile', [ProfileController::class, 'getProfile']);
    Route::post('/profile/update-foto', [ProfileController::class, 'updateFoto']);
    Route::post('/profile/update-data', [ProfileController::class, 'updateData']);
    Route::post('/profile/change-password', [ProfileController::class, 'changePassword']);


});
