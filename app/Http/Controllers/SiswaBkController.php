<?php

namespace App\Http\Controllers;

use App\Exports\LaporanAlphaExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel; 

class SiswaBkController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        // Ganti $daftarKelas menjadi $list_kelas agar sesuai dengan Blade
        $list_kelas = DB::table('kelas')->get();
        $selectedKelas = $request->get('kelas_id');

        $query = DB::table('siswa')
            ->join('kelas', 'siswa.kelas_id', '=', 'kelas.id')
            ->select('siswa.*', 'kelas.nama_kelas');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('siswa.nama_siswa', 'LIKE', "%{$search}%")
                    ->orWhere('siswa.nisn', 'LIKE', "%{$search}%");
            });
        }

        if ($selectedKelas) {
            $query->where('siswa.kelas_id', $selectedKelas);
        }

        $siswa = $query->paginate(15);

        // Pastikan compact-nya mengirim 'list_kelas'
        return view('dashboard.bk.profil_siswa', compact('siswa', 'list_kelas', 'selectedKelas'));
    }

    public function show($id)
    {
        $siswa = DB::table('siswa')
            ->join('kelas', 'siswa.kelas_id', '=', 'kelas.id')
            ->where('siswa.id', $id)
            ->select('siswa.*', 'kelas.nama_kelas')
            ->first();

        if (!$siswa) {
            return redirect()->back()->with('error', 'Siswa tidak ditemukan');
        }

        // Ambil histori Alpha dengan GroupBy agar mapel yang berdampingan di hari yang sama dihitung 1
        $historiAlpha = DB::table('presensi_detail')
            ->join('jurnals', 'presensi_detail.jurnal_id', '=', 'jurnals.id')
            ->join('jadwal_pelajaran', 'jurnals.jadwal_id', '=', 'jadwal_pelajaran.id')
            ->join('mata_pelajaran', 'jadwal_pelajaran.mapel_id', '=', 'mata_pelajaran.id')
            ->where('presensi_detail.siswa_id', $id)
            ->where('presensi_detail.status', 'Alpha')
            // Kita select kolom yang unik saja untuk dikelompokkan
            ->select('jurnals.tanggal', 'mata_pelajaran.nama_mapel')
            // Kelompokkan berdasarkan tanggal dan nama mapel
            ->groupBy('jurnals.tanggal', 'mata_pelajaran.nama_mapel')
            ->orderBy('jurnals.tanggal', 'desc')
            ->get();

        return view('dashboard.bk.profil_siswa', compact('siswa', 'historiAlpha'));
    }

    public function export(Request $request)
    {
        $nama_file = 'Laporan_Alpha_Siswa_' . date('Y-m-d_Hi') . '.xlsx';
        return Excel::download(new LaporanAlphaExport($request->all()), $nama_file);
    }
}
