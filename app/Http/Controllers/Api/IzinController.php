<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class IzinController extends Controller
{
    public function inputIzin(Request $request)
    {
        // 1. Validasi Inputan dari Aplikasi Wali Kelas
        $request->validate([
            'siswa_id' => 'required|integer',
            'status' => 'required|in:Sakit,Izin,Dispensasi',
            // File foto surat izin opsional, maksimal 2MB
            'foto_surat' => 'nullable|image|max:2048' 
        ]);

        // 2. Keamanan: Pastikan yang login BENAR-BENAR Wali Kelas dari siswa ini
        $guru_login_id = $request->user()->id;
        $siswa = DB::table('siswa')->where('id', $request->siswa_id)->first();
        $kelas = DB::table('kelas')->where('id', $siswa->kelas_id)->first();

        if ($kelas->wali_kelas_id !== $guru_login_id) {
            return response()->json([
                'success' => false, 
                'message' => 'Akses ditolak. Anda bukan Wali Kelas dari ' . $siswa->nama_siswa
            ], 403);
        }

        // 3. Cek apakah sudah diinput sebelumnya hari ini (mencegah dobel data)
        $hari_ini = Carbon::now()->format('Y-m-d');
        $cek_izin = DB::table('izin_siswa')
                      ->where('siswa_id', $request->siswa_id)
                      ->where('tanggal_izin', $hari_ini)
                      ->first();

        if ($cek_izin) {
            return response()->json(['success' => false, 'message' => 'Siswa sudah tercatat izin hari ini.'], 400);
        }

        // 4. SUKSES! Simpan data izin ke Database
        DB::table('izin_siswa')->insert([
            'siswa_id' => $request->siswa_id,
            'wali_kelas_id' => $guru_login_id,
            'tanggal_izin' => $hari_ini,
            'status' => $request->status,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil! Status ' . $siswa->nama_siswa . ' otomatis terkunci sebagai ' . $request->status . ' untuk semua mapel hari ini.'
        ], 200);
    }
}