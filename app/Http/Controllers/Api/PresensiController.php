<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PresensiController extends Controller
{
    public function scanQr(Request $request)
    {
        // 1. Validasi Input dari Kamera Flutter
        $request->validate([
            'jadwal_id' => 'required|integer',
            'qr_token' => 'required|string'
        ]);

        // 2. Cari Data Siswa berdasarkan QR Token
        $siswa = DB::table('siswa')->where('qr_token', $request->qr_token)->first();

        if (!$siswa) {
            return response()->json(['success' => false, 'message' => 'QR Code Tidak Dikenali (Siswa tidak ditemukan).'], 404);
        }

        // 3. Cek Apakah Siswa Berada di Kelas yang Tepat
        $jadwal = DB::table('jadwal_pelajaran')->where('id', $request->jadwal_id)->first();
        if ($siswa->kelas_id !== $jadwal->kelas_id) {
            return response()->json(['success' => false, 'message' => 'Siswa salah kelas! Ini bukan kelas ' . $siswa->nama_siswa . '.'], 403);
        }

        // 4. CEK LOGIKA PENGUNCIAN IZIN (Sinkronisasi Wali Kelas)
        $hari_ini = Carbon::now()->format('Y-m-d');
        $izin_wali_kelas = DB::table('izin_siswa')
                            ->where('siswa_id', $siswa->id)
                            ->where('tanggal_izin', $hari_ini)
                            ->first();

        if ($izin_wali_kelas) {
            return response()->json([
                'success' => false, 
                'message' => "Siswa sudah tercatat " . $izin_wali_kelas->status . " oleh Wali Kelas. Tidak perlu di-scan."
            ], 400);
        }

        // 5. Cek Apakah Siswa Sudah Di-Scan Sebelumnya (Mencegah Double Scan)
        $cek_presensi = DB::table('presensi_detail')
                            ->where('jadwal_id', $request->jadwal_id)
                            ->where('siswa_id', $siswa->id)
                            ->where('tanggal', $hari_ini)
                            ->first();

        if ($cek_presensi) {
            return response()->json(['success' => false, 'message' => 'Siswa ini sudah di-scan sebelumnya.'], 400);
        }

        // 6. SUKSES! Simpan Presensi sebagai "Hadir"
        DB::table('presensi_detail')->insert([
            'jadwal_id' => $request->jadwal_id,
            'siswa_id' => $siswa->id,
            'tanggal' => $hari_ini,
            'status' => 'Hadir',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil: ' . $siswa->nama_siswa . ' (Hadir)',
            'data_siswa' => $siswa
        ], 200);
    }
}