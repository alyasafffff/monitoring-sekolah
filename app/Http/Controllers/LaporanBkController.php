<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LaporanBkController extends Controller
{
    public function index(Request $request)
    {
        $daftarKelas = DB::table('kelas')->get();
        $selectedKelas = $request->get('kelas_id');
        $tglMulai = $request->get('tgl_mulai', date('Y-m-01')); 
        $tglSelesai = $request->get('tgl_selesai', date('Y-m-d')); 

        $query = DB::table('presensi_detail')
            ->join('jurnals', 'presensi_detail.jurnal_id', '=', 'jurnals.id')
            ->join('siswa', 'presensi_detail.siswa_id', '=', 'siswa.id')
            ->join('kelas', 'siswa.kelas_id', '=', 'kelas.id')
            ->join('jadwal_pelajaran', 'jurnals.jadwal_id', '=', 'jadwal_pelajaran.id')
            ->join('mata_pelajaran', 'jadwal_pelajaran.mapel_id', '=', 'mata_pelajaran.id')
            ->join('users', 'jadwal_pelajaran.guru_id', '=', 'users.id')
            ->join('jam_pelajaran_config', 'jadwal_pelajaran.jam_pelajaran_config_id', '=', 'jam_pelajaran_config.id')
            ->where('presensi_detail.status', 'Alpha')
            ->whereBetween('jurnals.tanggal', [$tglMulai, $tglSelesai]);

        if ($selectedKelas) {
            $query->where('siswa.kelas_id', $selectedKelas);
        }

        /** * LOGIKA PENGGABUNGAN (GROUPING):
         * Kita grupkan berdasarkan Siswa, Tanggal, dan Mapel.
         * Lalu kita ambil jam_mulai paling AWAL dan jam_selesai paling AKHIR.
         */
        $dataAlpha = $query->select(
            'siswa.nama_siswa',
            'kelas.nama_kelas',
            'mata_pelajaran.nama_mapel',
            'jurnals.tanggal',
            'users.name as nama_guru',
            DB::raw('MIN(jam_pelajaran_config.jam_mulai) as jam_mulai_gabung'),
            DB::raw('MAX(jam_pelajaran_config.jam_selesai) as jam_selesai_gabung')
        )
        ->groupBy(
            'siswa.id', 
            'siswa.nama_siswa', 
            'kelas.nama_kelas', 
            'mata_pelajaran.nama_mapel', 
            'jurnals.tanggal', 
            'users.name'
        )
        ->orderBy('jurnals.tanggal', 'desc')
        ->get();

        return view('dashboard.bk.laporan_alpha', compact('dataAlpha', 'daftarKelas', 'selectedKelas', 'tglMulai', 'tglSelesai'));
    }
}