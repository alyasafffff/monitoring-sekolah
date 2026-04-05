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
        // 2. LOGIKA GURU BK
        // 2. LOGIKA GURU BK
        // 2. LOGIKA GURU BK
        // 2. LOGIKA GURU BK
        if ($user->role === 'bk') {

            // Menghitung jumlah siswa unik yang bolos hari ini
            $totalBolosHariIni = DB::table('presensi_detail')
                ->join('jurnals', 'presensi_detail.jurnal_id', '=', 'jurnals.id')
                ->where('jurnals.tanggal', $hariIni)
                ->where('presensi_detail.status', 'Alpha')
                ->distinct('presensi_detail.siswa_id')
                ->count();

            $totalIzinSakit = DB::table('izin_siswa')
                ->where('tanggal_izin', $hariIni)
                ->count();

            // 1. SISWA BUTUH PERHATIAN (Bolos >= 3 Mapel Berbeda bulan ini)
            $siswaButuhPerhatian = DB::table('presensi_detail')
                ->join('jurnals', 'presensi_detail.jurnal_id', '=', 'jurnals.id')
                ->join('jadwal_pelajaran', 'jurnals.jadwal_id', '=', 'jadwal_pelajaran.id')
                ->where('presensi_detail.status', 'Alpha')
                ->whereMonth('jurnals.tanggal', Carbon::now()->month)
                ->whereYear('jurnals.tanggal', Carbon::now()->year)
                ->select('presensi_detail.siswa_id', 'jurnals.tanggal', 'jadwal_pelajaran.mapel_id')
                ->groupBy('presensi_detail.siswa_id', 'jurnals.tanggal', 'jadwal_pelajaran.mapel_id')
                ->get()
                ->groupBy('siswa_id')
                ->filter(function ($group) {
                    return $group->count() >= 3;
                })->count();

            // 2. LIVE FEED
            $liveBolos = DB::table('presensi_detail')
                ->join('jurnals', 'presensi_detail.jurnal_id', '=', 'jurnals.id')
                ->join('siswa', 'presensi_detail.siswa_id', '=', 'siswa.id')
                ->join('kelas', 'siswa.kelas_id', '=', 'kelas.id')
                ->join('jadwal_pelajaran', 'jurnals.jadwal_id', '=', 'jadwal_pelajaran.id')
                ->join('mata_pelajaran', 'jadwal_pelajaran.mapel_id', '=', 'mata_pelajaran.id')
                ->join('jam_pelajaran_config', 'jadwal_pelajaran.jam_pelajaran_config_id', '=', 'jam_pelajaran_config.id')
                ->where('jurnals.tanggal', $hariIni)
                ->where('presensi_detail.status', 'Alpha')
                ->select(
                    'siswa.nama_siswa',
                    'kelas.nama_kelas',
                    'mata_pelajaran.nama_mapel',
                    DB::raw('MIN(jam_pelajaran_config.jam_mulai) as jam_mulai'),
                    DB::raw('MAX(jam_pelajaran_config.jam_selesai) as jam_selesai'),
                    DB::raw('MAX(presensi_detail.created_at) as created_at')
                )
                ->groupBy('siswa.id', 'jurnals.tanggal', 'mata_pelajaran.id')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            // 3. TOP 5 (Gunakan cara CONCAT agar pasti terbaca oleh database)
            // 3. TOP 5 (Versi Terjamin Muncul)
            $siswaBermasalah = DB::table('presensi_detail')
                ->join('jurnals', 'presensi_detail.jurnal_id', '=', 'jurnals.id')
                ->join('siswa', 'presensi_detail.siswa_id', '=', 'siswa.id')
                ->join('kelas', 'siswa.kelas_id', '=', 'kelas.id')
                ->join('jadwal_pelajaran', 'jurnals.jadwal_id', '=', 'jadwal_pelajaran.id')
                // Kita filter 30 hari terakhir saja supaya data testing lama tetap muncul
                ->where('jurnals.tanggal', '>=', Carbon::now()->subDays(30))
                ->where('presensi_detail.status', 'Alpha')
                ->select(
                    'siswa.nama_siswa',
                    'kelas.nama_kelas',
                    // Kita hitung kombinasi Tanggal + Mapel agar sesi berurutan dihitung 1
                    DB::raw('COUNT(DISTINCT jurnals.tanggal, jadwal_pelajaran.mapel_id) as total_alpha')
                )
                ->groupBy('siswa.id', 'siswa.nama_siswa', 'kelas.nama_kelas')
                ->orderBy('total_alpha', 'desc')
                ->limit(5)
                ->get();
            return view('dashboard.bk', compact(
                'user',
                'totalBolosHariIni',
                'totalIzinSakit',
                'siswaButuhPerhatian',
                'liveBolos',
                'siswaBermasalah'
            ));
        }

        if ($user->role === 'kepsek') {
            $hariIni = Carbon::today()->toDateString();

            // 1. STATISTIK RINGKAS
            $totalSiswa = DB::table('siswa')->count();
            $totalGuru = DB::table('users')->where('role', 'guru')->count();
            $alphaHariIni = DB::table('presensi_detail')
                ->join('jurnals', 'presensi_detail.jurnal_id', '=', 'jurnals.id')
                ->where('jurnals.tanggal', $hariIni)
                ->where('presensi_detail.status', 'Alpha')
                ->distinct('presensi_detail.siswa_id')
                ->count();

            // 2. TREN PRESENSI (7 Hari Terakhir)
            $chartData = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                $count = DB::table('presensi_detail')
                    ->join('jurnals', 'presensi_detail.jurnal_id', '=', 'jurnals.id')
                    ->where('jurnals.tanggal', $date->toDateString())
                    ->where('presensi_detail.status', 'Alpha')
                    ->count();
                $chartData[] = ['label' => $date->format('d M'), 'value' => $count];
            }

            // 3. MONITOR KESELARASAN MATERI (Query yang sudah diperbaiki)
            $monitorMateri = DB::table('jurnals')
                ->join('jadwal_pelajaran', 'jurnals.jadwal_id', '=', 'jadwal_pelajaran.id')
                ->join('kelas', 'jadwal_pelajaran.kelas_id', '=', 'kelas.id') // Join kelas lewat jadwal
                ->join('mata_pelajaran', 'jadwal_pelajaran.mapel_id', '=', 'mata_pelajaran.id')
                ->join('users', 'jadwal_pelajaran.guru_id', '=', 'users.id')
                ->select(
                    'kelas.nama_kelas',
                    'mata_pelajaran.nama_mapel',
                    'users.name as nama_guru',
                    'jurnals.materi',
                    'jurnals.tanggal',
                    // Kita ambil karakter pertama dari nama_kelas sebagai jenjang
                    DB::raw('SUBSTRING(kelas.nama_kelas, 1, 1) as jenjang')
                )
                ->whereIn('jurnals.id', function ($query) {
                    $query->select(DB::raw('MAX(id)'))
                        ->from('jurnals')
                        ->groupBy('jadwal_id'); // Ambil materi terakhir per jadwal
                })
                ->orderBy('jenjang')
                ->orderBy('mata_pelajaran.nama_mapel')
                ->get();

            return view('dashboard.kepsek', compact(
                'user',
                'totalSiswa',
                'totalGuru',
                'alphaHariIni',
                'chartData',
                'monitorMateri'
            ));
        }

        return view('dashboard.guru_denied');
    }
}
