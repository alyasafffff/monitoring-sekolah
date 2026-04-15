<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class JadwalController extends Controller
{
    // 1. API UNTUK HALAMAN HOME (GET)
    public function index(Request $request)
    {
        $user = $request->user();
        Carbon::setLocale('id');
        $hariIni = Carbon::now()->isoFormat('dddd');
        $tanggalHariIni = Carbon::now()->format('Y-m-d');

        $jadwal = DB::table('jadwal_pelajaran')
            ->join('kelas', 'jadwal_pelajaran.kelas_id', '=', 'kelas.id')
            ->join('jam_pelajaran_config', 'jadwal_pelajaran.jam_pelajaran_config_id', '=', 'jam_pelajaran_config.id')
            // GANTI KE LEFT JOIN: Agar kegiatan yang mapel_id nya NULL tetap muncul
            ->leftJoin('mata_pelajaran', 'jadwal_pelajaran.mapel_id', '=', 'mata_pelajaran.id')
            ->leftJoin('kegiatan', 'jadwal_pelajaran.kegiatan_id', '=', 'kegiatan.id')
            ->where('jadwal_pelajaran.guru_id', $user->id)
            ->where('jadwal_pelajaran.hari', $hariIni)
            ->select(
                'jadwal_pelajaran.id',
                'kelas.nama_kelas',
                // LOGIKA: Jika nama_mapel NULL, ambil nama_kegiatan
                DB::raw('COALESCE(mata_pelajaran.nama_mapel, kegiatan.nama_kegiatan) as nama_mapel'),
                'jam_pelajaran_config.jam_mulai',
                'jam_pelajaran_config.jam_selesai',
                // Tambahkan tipe untuk membedakan di Flutter nanti
                'jam_pelajaran_config.tipe'
            )
            ->orderBy('jam_pelajaran_config.jam_mulai', 'asc')
            ->get();

        $dataLengkap = $jadwal->map(function ($item) use ($tanggalHariIni) {
            $jurnal = DB::table('jurnals')
                ->where('jadwal_id', $item->id)
                ->where('tanggal', $tanggalHariIni)
                ->first();

            $item->status_jurnal = $jurnal ? $jurnal->status_pengisian : 'belum_mulai';
            $item->jurnal_id = $jurnal ? $jurnal->id : null;

            return $item;
        });

        return response()->json([
            'success' => true,
            'message' => 'List Jadwal Hari Ini',
            'hari' => $hariIni,
            'data' => $dataLengkap
        ], 200);
    }

    // Tambahkan ini di JadwalController
    public function piketIndex(Request $request)
    {
        Carbon::setLocale('id');
        $hariIni = Carbon::now()->isoFormat('dddd');
        $tanggalHariIni = Carbon::now()->format('Y-m-d');
        $kelasId = $request->query('kelas_id');

        $query = DB::table('jadwal_pelajaran')
            ->join('kelas', 'jadwal_pelajaran.kelas_id', '=', 'kelas.id')
            ->join('jam_pelajaran_config', 'jadwal_pelajaran.jam_pelajaran_config_id', '=', 'jam_pelajaran_config.id')
            ->join('users', 'jadwal_pelajaran.guru_id', '=', 'users.id')
            ->leftJoin('mata_pelajaran', 'jadwal_pelajaran.mapel_id', '=', 'mata_pelajaran.id')
            ->where('jadwal_pelajaran.hari', $hariIni)
            ->where('jam_pelajaran_config.tipe', 'mapel');

        if ($kelasId) {
            $query->where('jadwal_pelajaran.kelas_id', $kelasId);
        }

        $rawJadwal = $query->select(
            'jadwal_pelajaran.id',
            'kelas.nama_kelas',
            'users.name as nama_guru_asli',
            'mata_pelajaran.nama_mapel',
            'jam_pelajaran_config.jam_mulai',
            'jam_pelajaran_config.jam_selesai',
            'jam_pelajaran_config.tipe'
        )
            ->orderBy('kelas.nama_kelas', 'asc') // Urutkan per kelas dulu
            ->orderBy('jam_pelajaran_config.jam_mulai', 'asc')
            ->get();

        // --- LOGIKA GROUPING (Peleburan Jam) ---
        $groupedJadwal = [];
        foreach ($rawJadwal as $current) {
            // Cek status jurnal untuk item ini
            $jurnal = DB::table('jurnals')
                ->where('jadwal_id', $current->id)
                ->where('tanggal', $tanggalHariIni)
                ->first();

            $current->status_jurnal = $jurnal ? $jurnal->status_pengisian : 'belum_mulai';
            $current->jurnal_id = $jurnal ? $jurnal->id : null;

            if (empty($groupedJadwal)) {
                $groupedJadwal[] = $current;
            } else {
                $lastIndex = count($groupedJadwal) - 1;
                $last = $groupedJadwal[$lastIndex];

                // Syarat Lebur: Mapel sama, Kelas sama, Guru sama, dan Jam Berurutan
                if (
                    $last->nama_mapel == $current->nama_mapel &&
                    $last->nama_kelas == $current->nama_kelas &&
                    $last->nama_guru_asli == $current->nama_guru_asli &&
                    $last->jam_selesai == $current->jam_mulai
                ) {
                    // Update jam selesai milik data terakhir dengan jam selesai data sekarang
                    $groupedJadwal[$lastIndex]->jam_selesai = $current->jam_selesai;
                } else {
                    $groupedJadwal[] = $current;
                }
            }
        }

        return response()->json([
            'success' => true,
            'data' => $groupedJadwal,
            'list_kelas' => DB::table('kelas')->select('id', 'nama_kelas')->get()
        ]);
    }
    public function mulaiKelas(Request $request)
    {
        $jadwalId = $request->jadwal_id;
        $tanggalHariIni = now()->format('Y-m-d');
        $userActive = $request->user(); // Guru yang sedang login (Piket atau Asli)

        // 1. Ambil data jadwal utama yang diklik
        $jadwalKlik = DB::table('jadwal_pelajaran')
            ->join('jam_pelajaran_config', 'jadwal_pelajaran.jam_pelajaran_config_id', '=', 'jam_pelajaran_config.id')
            ->where('jadwal_pelajaran.id', $jadwalId)
            ->select('jadwal_pelajaran.*', 'jam_pelajaran_config.jam_ke')
            ->first();

        if (!$jadwalKlik) {
            return response()->json(['success' => false, 'message' => 'Jadwal tidak ditemukan'], 404);
        }

        // 2. Cari semua sesi yang berdampingan (Mapel & Kelas yang sama di hari yang sama)
        // Filter guru_id dihilangkan agar Guru Piket bisa "menangkap" semua jam mapel tersebut
        $semuaSesi = DB::table('jadwal_pelajaran')
            ->join('jam_pelajaran_config', 'jadwal_pelajaran.jam_pelajaran_config_id', '=', 'jam_pelajaran_config.id')
            ->where('kelas_id', $jadwalKlik->kelas_id)
            ->where('mapel_id', $jadwalKlik->mapel_id)
            ->where('hari', $jadwalKlik->hari)
            ->select('jadwal_pelajaran.id', 'jam_pelajaran_config.jam_ke')
            ->get();

        return DB::transaction(function () use ($semuaSesi, $jadwalKlik, $tanggalHariIni, $userActive) {
            $jurnalIdBalikan = null;

            foreach ($semuaSesi as $sesi) {
                // Cek apakah jurnal sudah dibuat sebelumnya
                $cek = DB::table('jurnals')
                    ->where('jadwal_id', $sesi->id)
                    ->where('tanggal', $tanggalHariIni)
                    ->first();

                if (!$cek) {
                    // A. INSERT JURNAL BARU
                    // Kolom guru_id diisi dengan ID user yang sedang menekan tombol (Piket/Asli)
                    $idBaru = DB::table('jurnals')->insertGetId([
                        'jadwal_id' => $sesi->id,
                        'guru_id'   => $userActive->id, // <--- REKAM SIAPA YANG MENGAJAR
                        'tanggal'   => $tanggalHariIni,
                        'status_pengisian' => 'proses',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // B. LOGIKA IZIN OTOMATIS
                    $this->simpanDetailSiswa($idBaru, $jadwalKlik->kelas_id, $sesi->jam_ke, $tanggalHariIni);

                    if ($sesi->id == $jadwalKlik->id) {
                        $jurnalIdBalikan = $idBaru;
                    }
                } else {
                    // Jika sudah ada, gunakan yang sudah ada
                    if ($sesi->id == $jadwalKlik->id) {
                        $jurnalIdBalikan = $cek->id;
                    }
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $jurnalIdBalikan,
                    'message' => 'Kelas berhasil dimulai oleh ' . $userActive->name
                ]
            ]);
        });
    }
    // INI FUNGSI BANTUAN (Private Method) - Letakkan di bawah mulaiKelas (masih dalam satu class)
    private function simpanDetailSiswa($jurnalId, $kelasId, $jamKe, $tanggal)
    {
        $listSiswa = DB::table('siswa')->where('kelas_id', $kelasId)->get();
        $dataPresensi = [];

        foreach ($listSiswa as $siswa) {
            $izin = DB::table('izin_siswa')
                ->where('siswa_id', $siswa->id)
                ->where('tanggal_izin', $tanggal)
                ->where(function ($q) use ($jamKe) {
                    $q->whereNull('jam_ke_mulai')
                        ->orWhere(function ($q2) use ($jamKe) {
                            $q2->where('jam_ke_mulai', '<=', $jamKe)
                                ->where('jam_ke_selesai', '>=', $jamKe);
                        });
                })->first();

            $dataPresensi[] = [
                'jurnal_id'  => $jurnalId,
                'siswa_id'   => $siswa->id,
                'status'     => $izin ? $izin->status : 'Alpha',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('presensi_detail')->insert($dataPresensi);
    }
}
