<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JadwalWebController extends Controller
{
    public function index(Request $request)
    {
        $kelaslist = DB::table('kelas')->orderBy('nama_kelas', 'asc')->get();
        $jadwalMatrix = [];
        $kelas_terpilih = null;
        $mapel = [];
        $guru = [];
        $configJam = [];
        $maxJamKe = 0;

        if ($request->has('kelas_id')) {
            $kelas_terpilih = DB::table('kelas')->where('id', $request->kelas_id)->first();
            $mapel = DB::table('mata_pelajaran')->orderBy('nama_mapel')->get();
            $guru = DB::table('users')->where('role', 'guru')->orderBy('name')->get();

            // 1. Ambil Konfigurasi Jam yang Aktif
            $dbConfig = DB::table('jam_pelajaran_config')
                ->orderBy('jam_ke')
                ->get();

            $maxJamKe = $dbConfig->max('jam_ke') ?? 0;

            foreach ($dbConfig as $c) {
                $configJam[$c->hari_grup][$c->jam_ke] = [
                    'id' => $c->id,
                    'mulai' => \Carbon\Carbon::parse($c->jam_mulai)->format('H:i'),
                    'selesai' => \Carbon\Carbon::parse($c->jam_selesai)->format('H:i'),
                    'tipe' => $c->tipe,
                    'keterangan' => $c->keterangan
                ];
            }

            // 2. Ambil Jadwal yang sudah diisi
            $rawJadwal = DB::table('jadwal_pelajaran')
                ->join('mata_pelajaran', 'jadwal_pelajaran.mapel_id', '=', 'mata_pelajaran.id')
                ->join('users', 'jadwal_pelajaran.guru_id', '=', 'users.id')
                ->join('jam_pelajaran_config', 'jadwal_pelajaran.jam_pelajaran_config_id', '=', 'jam_pelajaran_config.id')
                ->where('jadwal_pelajaran.kelas_id', $request->kelas_id)
                ->select('jadwal_pelajaran.*', 'mata_pelajaran.nama_mapel', 'users.name as nama_guru', 'jam_pelajaran_config.hari_grup', 'jam_pelajaran_config.jam_mulai')
                ->get();

            // Di dalam fungsi index, ubah bagian foreach rawJadwal
            foreach ($rawJadwal as $j) {
                // Pastikan format jam mulai sama dengan yang ada di config ($configJam)
                $jamMulaiStr = \Carbon\Carbon::parse($j->jam_mulai)->format('H:i');

                // Sekarang matrix dipisahkan berdasarkan kolom HARI (bukan hari_grup)
                $jadwalMatrix[$j->hari][$jamMulaiStr] = $j;
            }
        }

        return view('dashboard.jadwal.index', compact('kelaslist', 'kelas_terpilih', 'configJam', 'maxJamKe', 'jadwalMatrix', 'mapel', 'guru'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required',
            'mapel_id' => 'required',
            'guru_id' => 'required',
            'jam_pelajaran_config_id' => 'required',
            'hari'     => 'required',
        ]);

        // 1. Cek bentrok guru di HARI dan JAM yang sama
        $bentrokGuru = DB::table('jadwal_pelajaran')
            ->where('guru_id', $request->guru_id)
            ->where('hari', $request->hari) // <--- Tambahkan ini
            ->where('jam_pelajaran_config_id', $request->jam_pelajaran_config_id)
            ->exists();

        if ($bentrokGuru) {
            return back()->with('error', 'Guru tersebut sudah mengajar di kelas lain pada hari dan jam ini!');
        }

        // 2. Cek apakah slot di kelas tersebut sudah terisi
        $slotTerisi = DB::table('jadwal_pelajaran')
            ->where('kelas_id', $request->kelas_id)
            ->where('hari', $request->hari) // <--- Tambahkan ini
            ->where('jam_pelajaran_config_id', $request->jam_pelajaran_config_id)
            ->exists();

        if ($slotTerisi) {
            return back()->with('error', 'Slot jam ini sudah terisi mata pelajaran lain!');
        }

        try {
            DB::table('jadwal_pelajaran')->insert([
                'kelas_id' => $request->kelas_id,
                'mapel_id' => $request->mapel_id,
                'guru_id' => $request->guru_id,
                'jam_pelajaran_config_id' => $request->jam_pelajaran_config_id,
                'hari'     => $request->hari,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            return redirect()->back()->with('success', 'Jadwal berhasil disimpan!');
        } catch (\Exception $e) {
            // Debugging: Jika masih error, gunakan return $e->getMessage(); untuk lihat error aslinya
            return redirect()->back()->with('error', 'Gagal menyimpan ke Database. Pastikan migration sudah diupdate.');
        }
    }

    public function destroy($id)
    {
        DB::table('jadwal_pelajaran')->where('id', $id)->delete();
        return redirect()->back()->with('success', 'Jadwal berhasil dihapus!');
    }
}
