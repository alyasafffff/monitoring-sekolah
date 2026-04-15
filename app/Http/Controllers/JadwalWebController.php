<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class JadwalWebController extends Controller
{
    public function index(Request $request)
    {
        $kelaslist = DB::table('kelas')->orderBy('nama_kelas', 'asc')->get();
        $kelas_id = $request->kelas_id;

        // Inisialisasi data default agar view tidak error saat kelas belum dipilih
        $data = [
            'kelaslist' => $kelaslist,
            'kelas_terpilih' => null,
            'configJam' => [],
            'maxJamKe' => 0,
            'jadwalMatrix' => []
        ];

        if ($kelas_id) {
            // Ambil data lengkap menggunakan fungsi helper di bawah
            $jadwalData = $this->getJadwalDataArray($kelas_id);
            $data = array_merge($data, $jadwalData);
        }

        return view('dashboard.jadwal.index', $data);
    }

    // FUNGSI HELPER: Agar logika query tidak ditulis dua kali (untuk Index & PDF)
    private function getJadwalDataArray($kelas_id)
    {
        $kelas_terpilih = DB::table('kelas')
            ->leftJoin('users', 'kelas.wali_kelas_id', '=', 'users.id')
            ->where('kelas.id', $kelas_id)
            ->select('kelas.*', 'users.name as nama_wali')
            ->first();

        $dbConfig = DB::table('jam_pelajaran_config')->orderBy('jam_ke')->get();
        $maxJamKe = $dbConfig->max('jam_ke') ?? 0;

        $configJam = [];
        foreach ($dbConfig as $c) {
            $configJam[$c->hari_grup][$c->jam_ke] = [
                'id' => $c->id,
                'mulai' => Carbon::parse($c->jam_mulai)->format('H:i'),
                'selesai' => Carbon::parse($c->jam_selesai)->format('H:i'),
                'tipe' => $c->tipe,
                'keterangan' => $c->keterangan
            ];
        }

        $rawJadwal = DB::table('jadwal_pelajaran')
            ->leftJoin('mata_pelajaran', 'jadwal_pelajaran.mapel_id', '=', 'mata_pelajaran.id')
            ->leftJoin('kegiatan', 'jadwal_pelajaran.kegiatan_id', '=', 'kegiatan.id')
            ->join('users', 'jadwal_pelajaran.guru_id', '=', 'users.id')
            ->join('jam_pelajaran_config', 'jadwal_pelajaran.jam_pelajaran_config_id', '=', 'jam_pelajaran_config.id')
            ->where('jadwal_pelajaran.kelas_id', $kelas_id)
            ->select(
                'jadwal_pelajaran.*',
                'mata_pelajaran.nama_mapel',
                'kegiatan.nama_kegiatan',
                'users.name as nama_guru',
                'jam_pelajaran_config.jam_mulai'
            )
            ->get();

        $jadwalMatrix = [];
        foreach ($rawJadwal as $j) {
            $jamMulaiStr = Carbon::parse($j->jam_mulai)->format('H:i');
            $jadwalMatrix[$j->hari][$jamMulaiStr] = $j;
        }

        return [
            'kelas_terpilih' => $kelas_terpilih,
            'configJam' => $configJam,
            'maxJamKe' => $maxJamKe,
            'jadwalMatrix' => $jadwalMatrix
        ];
    }

    public function exportPdf(Request $request)
    {
        $kelas_id = $request->kelas_id;
        if (!$kelas_id) return back();

        // Ambil data yang sama dengan yang tampil di web
        $data = $this->getJadwalDataArray($kelas_id);
        
        // Load view PDF (pastikan file resources/views/dashboard/jadwal/pdf.blade.php sudah ada)
        $pdf = Pdf::loadView('dashboard.jadwal.pdf', $data)
                  ->setPaper('a4', 'landscape'); 

        return $pdf->download('Jadwal_Kelas_' . $data['kelas_terpilih']->nama_kelas . '.pdf');
    }

    public function create(Request $request)
    {
        $kelas_id = $request->kelas_id;
        $hari = $request->hari;
        $config_id = $request->config_id;

        if (!$kelas_id || !$hari || !$config_id) {
            return redirect()->route('jadwal.index')->with('error', 'Akses tidak valid.');
        }

        $kelas = DB::table('kelas')->where('id', $kelas_id)->first();
        $mapel = DB::table('mata_pelajaran')->orderBy('nama_mapel')->get();
        $kegiatan = DB::table('kegiatan')->orderBy('nama_kegiatan')->get();
        $guru = DB::table('users')->where('role', 'guru')->orderBy('name')->get();
        $jam_terpilih = DB::table('jam_pelajaran_config')->where('id', $config_id)->first();

        return view('dashboard.jadwal.create', compact('kelas', 'mapel', 'kegiatan', 'guru', 'hari', 'jam_terpilih'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required',
            'hari' => 'required',
            'jam_pelajaran_config_id' => 'required',
        ]);

        if ($request->filled('kegiatan_id')) {
            $kelas = DB::table('kelas')->where('id', $request->kelas_id)->first();
            $guru_id = $kelas->wali_kelas_id;
            $mapel_id = null;
            $kegiatan_id = $request->kegiatan_id;
        } else {
            $guru_id = $request->guru_id;
            $mapel_id = $request->mapel_id;
            $kegiatan_id = null;
        }

        $bentrok = DB::table('jadwal_pelajaran')
            ->join('kelas', 'jadwal_pelajaran.kelas_id', '=', 'kelas.id')
            ->where('jadwal_pelajaran.guru_id', $guru_id)
            ->where('jadwal_pelajaran.hari', $request->hari)
            ->where('jadwal_pelajaran.jam_pelajaran_config_id', $request->jam_pelajaran_config_id)
            ->where('jadwal_pelajaran.kelas_id', '!=', $request->kelas_id)
            ->select('kelas.nama_kelas')
            ->first();

        if ($bentrok) {
            return back()->withInput()->with('error', "Gagal! Guru tersebut sudah mengajar di Kelas {$bentrok->nama_kelas} pada jam ini.");
        }

        try {
            DB::table('jadwal_pelajaran')->updateOrInsert(
                [
                    'kelas_id' => $request->kelas_id,
                    'hari' => $request->hari,
                    'jam_pelajaran_config_id' => $request->jam_pelajaran_config_id,
                ],
                [
                    'mapel_id' => $mapel_id,
                    'kegiatan_id' => $kegiatan_id,
                    'guru_id' => $guru_id,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );

            return redirect('/admin/jadwal?kelas_id=' . $request->kelas_id)
                ->with('success', 'Jadwal berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $jadwal = DB::table('jadwal_pelajaran')->where('id', $id)->first();
        if ($jadwal) {
            $kelas_id = $jadwal->kelas_id;
            DB::table('jadwal_pelajaran')->where('id', $id)->delete();
            return redirect('/admin/jadwal?kelas_id=' . $kelas_id)->with('success', 'Jadwal berhasil dihapus!');
        }
        return redirect()->back();
    }
}