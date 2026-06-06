<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JurnalController extends Controller
{
    // 1. AMBIL LIST SISWA UNTUK DIABSEN (GET)
    // Dipanggil saat masuk halaman AbsensiScreen
    // Di JurnalController.php fungsi getPresensiSiswa
    public function getPresensiSiswa(Request $request, $jurnal_id)
    {
        // 1. Ambil info sesi (JAM KE) dari jadwal yang terhubung dengan jurnal ini
        $jurnal = DB::table('jurnals')
            ->join('jadwal_pelajaran', 'jurnals.jadwal_id', '=', 'jadwal_pelajaran.id')
            ->join('jam_pelajaran_config', 'jadwal_pelajaran.jam_pelajaran_config_id', '=', 'jam_pelajaran_config.id')
            ->where('jurnals.id', $jurnal_id)
            ->select('jurnals.tanggal', 'jam_pelajaran_config.jam_ke') // <--- KITA AMBIL JAM_KE (Integer)
            ->first();

        if (!$jurnal) {
            return response()->json(['success' => false, 'message' => 'Jurnal tidak ditemukan'], 404);
        }

        $jamKeSekarang = $jurnal->jam_ke; // Misal: 1, 2, 3
        $tanggalJurnal = $jurnal->tanggal;

        // 2. Query Detail Siswa + Cek Izin berdasarkan JAM KE
        $data = DB::table('presensi_detail')
            ->join('siswa', 'presensi_detail.siswa_id', '=', 'siswa.id')
            // Join ke tabel izin dengan filter sesi yang presisi
            ->leftJoin('izin_siswa', function ($join) use ($tanggalJurnal, $jamKeSekarang) {
                $join->on('presensi_detail.siswa_id', '=', 'izin_siswa.siswa_id')
                    ->where('izin_siswa.tanggal_izin', '=', $tanggalJurnal)
                    ->where(function ($q) use ($jamKeSekarang) {
                        // Logika: Izin seharian (jam_ke_mulai NULL) 
                        // ATAU jam_ke_mulai <= sesi sekarang DAN jam_ke_selesai >= sesi sekarang
                        $q->whereNull('izin_siswa.jam_ke_mulai')
                            ->orWhere(function ($q2) use ($jamKeSekarang) {
                                $q2->where('izin_siswa.jam_ke_mulai', '<=', $jamKeSekarang)
                                    ->where('izin_siswa.jam_ke_selesai', '>=', $jamKeSekarang);
                            });
                    });
            })
            ->where('presensi_detail.jurnal_id', $jurnal_id)
            ->select(
                'presensi_detail.id',
                'presensi_detail.siswa_id',
                'presensi_detail.status',
                'siswa.nama_siswa',
                'siswa.jenis_kelamin',
                'siswa.qr_token',
                // Logika Locked: Jika ditemukan record di leftJoin izin_siswa, maka is_locked = 1
                DB::raw('CASE WHEN izin_siswa.id IS NOT NULL THEN 1 ELSE 0 END as is_locked')
            )
            ->orderBy('siswa.nama_siswa', 'asc')
            ->get();

        return response()->json(['success' => true, 'data' => $data]);
    }

    // 2. SIMPAN JURNAL & UPDATE ABSEN (POST)
    // Dipanggil saat tombol "Simpan" ditekan
    public function updateJurnal(Request $request, $jurnal_id)
    {
        // Validasi data yang masuk dari Flutter
        $request->validate([
            'materi' => 'required|string',
            'catatan' => 'nullable|string',
            'siswa' => 'required|array',
            'siswa.*.siswa_id' => 'required|integer', // Menangkap siswa_id yang baru kita tambah di Flutter
            'siswa.*.status' => 'required|in:Hadir,Sakit,Izin,Alpha',
        ]);

        try {
            DB::beginTransaction();

            // 1. Cari info jurnal utama yang sedang diproses
            $jurnalUtama = DB::table('jurnals')
                ->join('jadwal_pelajaran', 'jurnals.jadwal_id', '=', 'jadwal_pelajaran.id')
                ->where('jurnals.id', $jurnal_id)
                ->select('jurnals.tanggal', 'jadwal_pelajaran.kelas_id', 'jadwal_pelajaran.mapel_id', 'jadwal_pelajaran.guru_id')
                ->first();

            if (!$jurnalUtama) {
                return response()->json(['success' => false, 'message' => 'Jurnal tidak ditemukan'], 404);
            }

            // 2. Cari semua ID jurnal "saudara" (Mapel & Kelas yang sama di hari itu)
            $idsJurnalTerkait = DB::table('jurnals')
                ->join('jadwal_pelajaran', 'jurnals.jadwal_id', '=', 'jadwal_pelajaran.id')
                ->where('jadwal_pelajaran.kelas_id', $jurnalUtama->kelas_id)
                ->where('jadwal_pelajaran.mapel_id', $jurnalUtama->mapel_id)
                ->where('jadwal_pelajaran.guru_id', $jurnalUtama->guru_id)
                ->where('jurnals.tanggal', $jurnalUtama->tanggal)
                ->pluck('jurnals.id');

            // 3. Update SEMUA jurnal terkait secara sekaligus
            foreach ($idsJurnalTerkait as $id) {
                // Update Header Jurnal (Materi & Catatan)
                DB::table('jurnals')
                    ->where('id', $id)
                    ->update([
                        'materi' => $request->materi,
                        'catatan' => $request->catatan,
                        'status_pengisian' => 'selesai',
                        'updated_at' => now(),
                    ]);

                // Update Status Presensi per Siswa untuk Jurnal ini
                foreach ($request->siswa as $s) {
                    DB::table('presensi_detail')
                        ->where('jurnal_id', $id)
                        ->where('siswa_id', $s['siswa_id']) // Menggunakan siswa_id sebagai kunci sinkronisasi
                        ->update([
                            'status' => $s['status'],
                            'updated_at' => now(),
                        ]);
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Semua sesi berhasil disimpan!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // 3. GET RIWAYAT MENGAJAR
    // 3. GET RIWAYAT MENGAJAR
    // 3. GET RIWAYAT MENGAJAR
    // 3. GET RIWAYAT MENGAJAR (VERSI PELEBURAN JAM GANDA SINKRON)
    // 3. GET RIWAYAT MENGAJAR (REVISI: GRUP MENYATU UNTUK UTAMA & PIKET)
    public function getRiwayat(Request $request)
    {
        $user = $request->user();

        // 1. Ambil data asli dari database
        $rawData = DB::table('jurnals')
            ->join('jadwal_pelajaran', 'jurnals.jadwal_id', '=', 'jadwal_pelajaran.id')
            ->join('kelas', 'jadwal_pelajaran.kelas_id', '=', 'kelas.id')
            ->join('mata_pelajaran', 'jadwal_pelajaran.mapel_id', '=', 'mata_pelajaran.id')
            ->join('jam_pelajaran_config', 'jadwal_pelajaran.jam_pelajaran_config_id', '=', 'jam_pelajaran_config.id')
            ->join('users as pengisi', 'jurnals.guru_id', '=', 'pengisi.id')
            ->join('users as guru_asli', 'jadwal_pelajaran.guru_id', '=', 'guru_asli.id')
            ->where(function ($query) use ($user) {
                $query->where('jurnals.guru_id', $user->id)
                    ->orWhere('jadwal_pelajaran.guru_id', $user->id);
            })
            ->select(
                'jurnals.id',
                'jurnals.tanggal',
                'jurnals.materi',
                'jurnals.status_pengisian',
                'kelas.nama_kelas',
                'mata_pelajaran.nama_mapel',
                'jam_pelajaran_config.jam_mulai',
                'jam_pelajaran_config.jam_selesai',
                'pengisi.name as nama_pengisi', 
                'guru_asli.name as nama_guru_asli', 
                DB::raw('CASE 
                    WHEN jurnals.guru_id = ' . $user->id . ' AND jadwal_pelajaran.guru_id = ' . $user->id . ' THEN "asli"
                    WHEN jurnals.guru_id = ' . $user->id . ' AND jadwal_pelajaran.guru_id != ' . $user->id . ' THEN "piket"
                    ELSE "digantikan"
                END as tipe_mengajar'),
                DB::raw('(SELECT COUNT(*) FROM presensi_detail WHERE presensi_detail.jurnal_id = jurnals.id AND status = "Hadir") as hadir'),
                DB::raw('(SELECT COUNT(*) FROM presensi_detail WHERE presensi_detail.jurnal_id = jurnals.id AND status = "Sakit") as sakit'),
                DB::raw('(SELECT COUNT(*) FROM presensi_detail WHERE presensi_detail.jurnal_id = jurnals.id AND status = "Izin") as izin'),
                DB::raw('(SELECT COUNT(*) FROM presensi_detail WHERE presensi_detail.jurnal_id = jurnals.id AND status = "Alpha") as alpha')
            )
            // KUNCI REVISI UTAMA: Kelompokkan per kelas dan mapel dulu, baru urutkan jam mulainya
            ->orderBy('jurnals.tanggal', 'desc')
            ->orderBy('kelas.nama_kelas', 'asc')
            ->orderBy('mata_pelajaran.nama_mapel', 'asc')
            ->orderBy('jam_pelajaran_config.jam_mulai', 'asc')
            ->get();

        // 2. --- LOGIKA PELEBURAN JAM ---
        $groupedRiwayat = [];

        foreach ($rawData as $current) {
            if (empty($groupedRiwayat)) {
                $groupedRiwayat[] = $current;
            } else {
                $lastIndex = count($groupedRiwayat) - 1;
                $last = $groupedRiwayat[$lastIndex];

                // Syarat Lebur Sesi Block: Tanggal, Mapel, Kelas, Status, Tipe Tugas sama, dan Jam Nempel
                if (
                    $last->tanggal == $current->tanggal &&
                    $last->nama_mapel == $current->nama_mapel &&
                    $last->nama_kelas == $current->nama_kelas &&
                    $last->tipe_mengajar == $current->tipe_mengajar &&
                    $last->status_pengisian == $current->status_pengisian &&
                    $last->jam_selesai == $current->jam_mulai
                ) {
                    // Perpanjang jam selesai data terakhir dengan jam selesai sesi saat ini
                    $groupedRiwayat[$lastIndex]->jam_selesai = $current->jam_selesai;
                } else {
                    $groupedRiwayat[] = $current;
                }
            }
        }

        // 3. REVISI URUTAN AKHIR: Balikkan kembali list yang sudah bersih 
        // agar riwayat mengajar JAM PALING SIANG (terbaru) tetap berada di posisi teratas HP
        $finalResult = array_reverse($groupedRiwayat);

        return response()->json([
            'success' => true,
            'data' => $finalResult
        ]);
    }
}
