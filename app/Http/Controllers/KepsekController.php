<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KepsekController extends Controller
{
    /**
     * Monitoring Keselarasan Materi Jurnal
     */
    public function monitoringJurnal(Request $request)
    {
        $jenjang = $request->get('jenjang', '7');

        // Logika navigasi minggu (Default: Minggu ini)
        $copyDate = $request->has('rel_date') ? Carbon::parse($request->rel_date) : Carbon::now();
        $startOfWeek = $copyDate->copy()->startOfWeek(); // Senin
        $endOfWeek = $copyDate->copy()->endOfWeek();     // Minggu

        $kelasList = DB::table('kelas')
            ->where('nama_kelas', 'LIKE', $jenjang . '%')
            ->orderBy('nama_kelas')
            ->get();

        $mapels = DB::table('mata_pelajaran')
            ->join('jadwal_pelajaran', 'mata_pelajaran.id', '=', 'jadwal_pelajaran.mapel_id')
            ->join('kelas', 'jadwal_pelajaran.kelas_id', '=', 'kelas.id')
            ->where('kelas.nama_kelas', 'LIKE', $jenjang . '%')
            ->select('mata_pelajaran.id', 'mata_pelajaran.nama_mapel')
            ->distinct()
            ->get();

        // Ambil materi TERAKHIR dalam RENTANG MINGGU tersebut
        $dataJurnal = DB::table('jurnals')
            ->join('jadwal_pelajaran', 'jurnals.jadwal_id', '=', 'jadwal_pelajaran.id')
            ->join('kelas', 'jadwal_pelajaran.kelas_id', '=', 'kelas.id')
            ->join('users', 'jadwal_pelajaran.guru_id', '=', 'users.id')
            ->join('mata_pelajaran', 'jadwal_pelajaran.mapel_id', '=', 'mata_pelajaran.id')
            ->where('kelas.nama_kelas', 'LIKE', $jenjang . '%')
            ->whereBetween('jurnals.tanggal', [$startOfWeek->toDateString(), $endOfWeek->toDateString()])
            ->select(
                'jadwal_pelajaran.mapel_id',
                'jadwal_pelajaran.kelas_id',
                'jurnals.materi',
                'jurnals.tanggal',
                'users.name as nama_guru'
            )
            ->get();

        return view('dashboard.kepsek.monitoring_jurnal', compact(
            'kelasList',
            'mapels',
            'dataJurnal',
            'jenjang',
            'startOfWeek',
            'endOfWeek'
        ));
    }

    /**
     * Monitoring Presensi (Statistik Per Kelas)
     */
    public function monitoringPresensi(Request $request)
    {
        // Pastikan menangkap input bulan/tahun dengan benar
        $bulan = $request->has('bulan') ? (int)$request->bulan : (int)date('m');
        $tahun = $request->has('tahun') ? (int)$request->tahun : (int)date('Y');

        // 1. REKAP PER KELAS
        // Kita hitung berdasarkan 'presensi_detail' yang punya 'jurnal' di bulan/tahun tersebut
        $rekapKelas = DB::table('kelas')
            ->select(
                'kelas.nama_kelas',
                // Logika: 1 Hari + 1 Siswa + 1 Mapel = 1 Kejadian (Distinct)
                DB::raw("COUNT(DISTINCT CASE WHEN pd.status = 'Alpha' THEN CONCAT(j.tanggal, '-', s.id, '-', jp.mapel_id) END) as total_alpha"),
                DB::raw("COUNT(DISTINCT CASE WHEN pd.status = 'Izin' THEN CONCAT(j.tanggal, '-', s.id, '-', jp.mapel_id) END) as total_izin"),
                DB::raw("COUNT(DISTINCT CASE WHEN pd.status = 'Sakit' THEN CONCAT(j.tanggal, '-', s.id, '-', jp.mapel_id) END) as total_sakit")
            )
            ->leftJoin('siswa as s', 'kelas.id', '=', 's.kelas_id')
            ->leftJoin('presensi_detail as pd', 's.id', '=', 'pd.siswa_id')
            ->leftJoin('jurnals as j', 'pd.jurnal_id', '=', 'j.id')
            ->leftJoin('jadwal_pelajaran as jp', 'j.jadwal_id', '=', 'jp.id')
            // Filter ditaruh di WHERE agar lebih akurat memfilter hasil join
            ->where(function ($query) use ($bulan, $tahun) {
                $query->whereMonth('j.tanggal', $bulan)
                    ->whereYear('j.tanggal', $tahun)
                    ->orWhereNull('j.tanggal'); // Biar kelas kosong tetap tampil
            })
            ->groupBy('kelas.id', 'kelas.nama_kelas')
            ->orderBy('total_alpha', 'desc')
            ->get();

        // 2. SISWA BERMASALAH (TOP 10)
        $siswaBermasalah = DB::table('siswa')
            ->join('kelas', 'siswa.kelas_id', '=', 'kelas.id')
            ->join('presensi_detail as pd', 'siswa.id', '=', 'pd.siswa_id')
            ->join('jurnals as j', 'pd.jurnal_id', '=', 'j.id')
            ->join('jadwal_pelajaran as jp', 'j.jadwal_id', '=', 'jp.id')
            ->where('pd.status', 'Alpha')
            ->whereMonth('j.tanggal', $bulan)
            ->whereYear('j.tanggal', $tahun)
            ->select(
                'siswa.nama_siswa',
                'kelas.nama_kelas',
                'siswa.no_hp_ortu',
                // Hitung berapa kali dia bolos (per pertemuan mapel)
                DB::raw("COUNT(DISTINCT CONCAT(j.tanggal, '-', jp.mapel_id)) as jumlah_alpha")
            )
            ->groupBy('siswa.id', 'siswa.nama_siswa', 'kelas.nama_kelas', 'siswa.no_hp_ortu')
            ->orderBy('jumlah_alpha', 'desc')
            ->limit(10)
            ->get();

        return view('dashboard.kepsek.monitoring_presensi', compact('rekapKelas', 'siswaBermasalah', 'bulan', 'tahun'));
    }
}
