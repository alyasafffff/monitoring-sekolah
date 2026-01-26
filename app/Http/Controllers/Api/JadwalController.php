<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
// ---> TAMBAHAN: Panggil Model yang dibutuhkan <---
use App\Models\Jadwal;
use App\Models\Presensi;

class JadwalController extends Controller
{
    public function jadwalHariIni(Request $request)
    {
        // 1. Ambil ID Guru yang sedang login (lewat token)
        $guru_id = $request->user()->id;

        // 2. Deteksi Hari Ini dalam Bahasa Indonesia (Senin, Selasa, dst)
        Carbon::setLocale('id');
        $hari_ini = Carbon::now()->isoFormat('dddd'); 

        // 3. Ambil data jadwal dari database (Gabungkan dengan tabel kelas & mapel)
        $jadwal = DB::table('jadwal_pelajaran')
            ->join('kelas', 'jadwal_pelajaran.kelas_id', '=', 'kelas.id')
            ->join('mata_pelajaran', 'jadwal_pelajaran.mapel_id', '=', 'mata_pelajaran.id')
            ->where('jadwal_pelajaran.guru_id', $guru_id)
            ->where('jadwal_pelajaran.hari', $hari_ini)
            ->select(
                'jadwal_pelajaran.id as jadwal_id',
                'kelas.nama_kelas',
                'mata_pelajaran.nama_mapel',
                'jadwal_pelajaran.jam_mulai',
                'jadwal_pelajaran.jam_selesai',
                'jadwal_pelajaran.jurnal_materi'
            )
            ->orderBy('jadwal_pelajaran.jam_mulai', 'asc')
            ->get();

        // 4. Kirim balasan ke Flutter
        if($jadwal->isEmpty()){
            return response()->json([
                'success' => true,
                'message' => 'Tidak ada jadwal mengajar hari ini.',
                'hari' => $hari_ini,
                'data' => []
            ], 200);
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil jadwal.',
            'hari' => $hari_ini,
            'data' => $jadwal
        ], 200);
    }

    public function mulaiKelas(Request $request)
    {
        // 1. Validasi input (Menggunakan nama tabel jadwal_pelajaran)
        $request->validate([
            'jadwal_id' => 'required|exists:jadwal_pelajaran,id'
        ]);

        // 2. Ambil data jadwal
        $jadwal = Jadwal::find($request->jadwal_id);

        // 3. Pastikan milik guru yang login
        if ($jadwal->guru_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak berhak mengakses kelas ini.'
            ], 403);
        }

        // 4. Cek apakah kelas ini sudah dibuka hari ini (biar tidak dobel)
        $presensiHariIni = Presensi::where('jadwal_id', $jadwal->id)
                                   ->where('tanggal', now()->toDateString())
                                   ->first();

        if ($presensiHariIni) {
            return response()->json([
                'success' => true,
                'message' => 'Kelas ini sudah dimulai sebelumnya.',
                'data' => [
                    'presensi_id' => $presensiHariIni->id
                ]
            ], 200);
        }

        // 5. Buka SESI KELAS baru di tabel presensi
        $presensi = Presensi::create([
            'jadwal_id' => $jadwal->id,
            'tanggal' => now()->toDateString(),
            'status' => 'buka' // Guru siap melakukan scan
        ]);

        // 6. Kembalikan ID Presensi ke aplikasi
        return response()->json([
            'success' => true,
            'message' => 'Kelas berhasil dimulai. Silakan scan kartu siswa.',
            'data' => [
                'presensi_id' => $presensi->id
            ]
        ], 201);
    }
}