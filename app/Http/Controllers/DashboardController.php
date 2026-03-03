<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $hariIni = Carbon::today()->toDateString();

        // 1. LOGIKA ADMIN (TETAP SEPERTI ASLINYA)
        if ($user->role === 'admin') {
            $totalSiswa = DB::table('siswa')->count();
            $totalGuru   = DB::table('users')->where('role', 'guru')->count();
            $totalKelas  = DB::table('kelas')->count();

            $izinTerbaru = DB::table('izin_siswa')
                ->join('siswa', 'izin_siswa.siswa_id', '=', 'siswa.id')
                ->join('kelas', 'siswa.kelas_id', '=', 'kelas.id')
                ->select('izin_siswa.*', 'siswa.nama_siswa', 'kelas.nama_kelas')
                ->orderBy('izin_siswa.created_at', 'desc')
                ->limit(5)
                ->get();

            return view('dashboard.admin', compact(
                'user',
                'totalSiswa',
                'totalGuru',
                'totalKelas',
                'izinTerbaru'
            ));
        }

        // 2. LOGIKA GURU BK (MEMANTAU 15 KELAS - REAL TIME)
        if ($user->role === 'bk') {

            // A. Ringkasan Statistik Global (Seluruh Sekolah)
            $totalBolosHariIni = DB::table('presensi_detail')
                ->join('jurnals', 'presensi_detail.jurnal_id', '=', 'jurnals.id')
                ->where('jurnals.tanggal', $hariIni)
                ->where('presensi_detail.status', 'Alpha') // Menggunakan kata lengkap sesuai DB
                ->distinct('presensi_detail.siswa_id')
                ->count();

            $totalIzinSakit = DB::table('izin_siswa')
                ->where('tanggal_izin', $hariIni)
                ->count();

            // Monitoring Guru: Berapa kelas yang sudah diabsen hari ini dari total 15 kelas?
            $kelasSudahPresensi = DB::table('jurnals')
                ->join('jadwal_pelajaran', 'jurnals.jadwal_id', '=', 'jadwal_pelajaran.id')
                ->where('jurnals.tanggal', $hariIni)
                ->distinct('jadwal_pelajaran.kelas_id')
                ->count();

            // B. Live Feed: 10 Siswa Terakhir yang Terdeteksi Alpha oleh Guru Mapel
            // 1. Daftar Live Siswa Bolos (Sudah disesuaikan dengan nama tabel mata_pelajaran)
            $liveBolos = DB::table('presensi_detail')
                ->join('jurnals', 'presensi_detail.jurnal_id', '=', 'jurnals.id')
                ->join('siswa', 'presensi_detail.siswa_id', '=', 'siswa.id')
                ->join('kelas', 'siswa.kelas_id', '=', 'kelas.id')
                ->join('jadwal_pelajaran', 'jurnals.jadwal_id', '=', 'jadwal_pelajaran.id')
                ->join('mata_pelajaran', 'jadwal_pelajaran.mapel_id', '=', 'mata_pelajaran.id')
                // Join ke config jam untuk mendapatkan waktu pelajaran
                ->join('jam_pelajaran_config', 'jadwal_pelajaran.jam_pelajaran_config_id', '=', 'jam_pelajaran_config.id') 
                ->where('jurnals.tanggal', $hariIni)
                ->where('presensi_detail.status', 'Alpha')
                ->select(
                    'siswa.nama_siswa',
                    'kelas.nama_kelas',
                    'mata_pelajaran.nama_mapel',
                    'jam_pelajaran_config.jam_mulai', // Ambil jam mulai
                    'jam_pelajaran_config.jam_selesai', // Ambil jam selesai
                    'presensi_detail.created_at'
                )
                ->orderBy('presensi_detail.created_at', 'desc')
                ->limit(10)
                ->get();
            // C. Top 5 Siswa Bermasalah (Paling sering Alpha bulan ini - Untuk Panggilan Ortu)
            $siswaBermasalah = DB::table('presensi_detail')
                ->join('siswa', 'presensi_detail.siswa_id', '=', 'siswa.id')
                ->join('kelas', 'siswa.kelas_id', '=', 'kelas.id')
                ->whereMonth('presensi_detail.created_at', Carbon::now()->month)
                ->where('presensi_detail.status', 'Alpha')
                ->select('siswa.nama_siswa', 'kelas.nama_kelas', DB::raw('count(*) as total_alpha'))
                ->groupBy('siswa.id', 'siswa.nama_siswa', 'kelas.nama_kelas')
                ->orderBy('total_alpha', 'desc')
                ->limit(5)
                ->get();

            return view('dashboard.bk', compact(
                'user',
                'totalBolosHariIni',
                'totalIzinSakit',
                'kelasSudahPresensi',
                'liveBolos',
                'siswaBermasalah'
            ));
        }

        // 3. LOGIKA KEPSEK (BISA COPAS DARI ADMIN JIKA MAU SAMA)
        if ($user->role === 'kepsek') {
            return view('dashboard.kepsek', compact('user'));
        }

        return view('dashboard.guru_denied');
    }
}
