<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class JurnalController extends Controller
{
    public function simpanJurnal(Request $request)
    {
        // 1. Validasi Inputan Guru (Jurnal KBM)
        $request->validate([
            'jadwal_id' => 'required|integer',
            'materi' => 'required|string',
            'foto_kelas' => 'nullable|image|max:2048' // Jika ada fitur upload foto
        ]);

        $hari_ini = Carbon::now()->format('Y-m-d');
        $waktu_sekarang = Carbon::now()->format('H:i:s');
        $jadwal = DB::table('jadwal_pelajaran')->where('id', $request->jadwal_id)->first();

        // 2. Simpan Jurnal Materi ke Database
        DB::table('jadwal_pelajaran')
            ->where('id', $request->jadwal_id)
            ->update([
                'jurnal_materi' => $request->materi,
                'updated_at' => now()
            ]);

        // ========================================================
        // 3. LOGIKA SMART DETECTION: Cari Siswa Alpha & Kirim ke BK
        // ========================================================

        // A. Ambil semua siswa di kelas ini
        $semua_siswa = DB::table('siswa')->where('kelas_id', $jadwal->kelas_id)->pluck('id')->toArray();

        // B. Ambil siswa yang tadi HADIR (sudah scan QR)
        $siswa_hadir = DB::table('presensi_detail')
                        ->where('jadwal_id', $jadwal->id)
                        ->where('tanggal', $hari_ini)
                        ->where('status', 'Hadir')
                        ->pluck('siswa_id')->toArray();

        // C. Ambil siswa yang punya IZIN/SAKIT dari Wali Kelas hari ini
        $siswa_izin = DB::table('izin_siswa')
                        ->whereIn('siswa_id', $semua_siswa)
                        ->where('tanggal_izin', $hari_ini)
                        ->pluck('siswa_id')->toArray();

        // D. RUMUS ALPHA: Siswa Alpha = (Semua Siswa) - (Hadir + Izin)
        $siswa_aman = array_merge($siswa_hadir, $siswa_izin);
        $siswa_alpha = array_diff($semua_siswa, $siswa_aman);

        // 4. EKSEKUSI: Masukkan ke tabel Alpha & Notifikasi BK
        foreach ($siswa_alpha as $alpha_id) {
            // Catat presensinya sebagai Alpha
            DB::table('presensi_detail')->insert([
                'jadwal_id' => $jadwal->id,
                'siswa_id' => $alpha_id,
                'tanggal' => $hari_ini,
                'status' => 'Alpha',
                'created_at' => now(),
            ]);

            // KIRIM NOTIFIKASI KE DASHBOARD BK (Insert ke log_pelanggaran_bk)
            DB::table('log_pelanggaran_bk')->insert([
                'siswa_id' => $alpha_id,
                'jadwal_id' => $jadwal->id,
                'tanggal' => $hari_ini,
                'jam_kejadian' => $waktu_sekarang,
                'status_penanganan' => 'Belum', // Status menunggu tindakan BK
                'created_at' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Jurnal berhasil disimpan. Sistem mendeteksi ' . count($siswa_alpha) . ' siswa Alpha dan telah dilaporkan ke BK.',
            'jumlah_alpha' => count($siswa_alpha)
        ], 200);
    }
}