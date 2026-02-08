<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JurnalController extends Controller
{
    // 1. AMBIL LIST SISWA UNTUK DIABSEN (GET)
    // Dipanggil saat masuk halaman AbsensiScreen
    public function getPresensiSiswa(Request $request, $jurnal_id)
    {
        // 1. Ambil Tanggal Jurnal dulu
        $jurnal = DB::table('jurnals')->where('id', $jurnal_id)->first();
        $tanggalJurnal = $jurnal->tanggal;

        // 2. Query Data Siswa + Cek Izin (Locked status)
        $data = DB::table('presensi_detail')
            ->join('siswa', 'presensi_detail.siswa_id', '=', 'siswa.id')
            // Join ke tabel izin untuk cek apakah ada izin hari ini
            ->leftJoin('izin_siswa', function($join) use ($tanggalJurnal) {
                $join->on('presensi_detail.siswa_id', '=', 'izin_siswa.siswa_id')
                     ->where('izin_siswa.tanggal_izin', '=', $tanggalJurnal);
            })
            ->where('presensi_detail.jurnal_id', $jurnal_id)
            ->select(
                'presensi_detail.id',
                'presensi_detail.siswa_id',
                'presensi_detail.status',     
                'siswa.nama_siswa',
                'siswa.jenis_kelamin',
                'siswa.qr_token',
                // Logika Locked: Jika ID izin ada (tidak null), maka is_locked = 1 (true)
                DB::raw('CASE WHEN izin_siswa.id IS NOT NULL THEN 1 ELSE 0 END as is_locked') 
            )
            ->orderBy('siswa.nama_siswa', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    // 2. SIMPAN JURNAL & UPDATE ABSEN (POST)
    // Dipanggil saat tombol "Simpan" ditekan
    public function updateJurnal(Request $request, $jurnal_id)
    {
        $request->validate([
            'materi' => 'required|string',
            'catatan' => 'nullable|string', // Validasi catatan
            'siswa' => 'required|array',
            'siswa.*.id' => 'required|integer',
            'siswa.*.status' => 'required|in:Hadir,Sakit,Izin,Alpha',
        ]);

        try {
            DB::beginTransaction();

            DB::table('jurnals')
                ->where('id', $jurnal_id)
                ->update([
                    'materi' => $request->materi,
                    'catatan' => $request->catatan, // Simpan ke kolom baru
                    'status_pengisian' => 'selesai',
                    'updated_at' => now(),
                ]);

            foreach ($request->siswa as $item) {
                DB::table('presensi_detail')
                    ->where('id', $item['id'])
                    ->update([
                        'status' => $item['status'],
                        'updated_at' => now(),
                    ]);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Jurnal berhasil disimpan!']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // 3. GET RIWAYAT MENGAJAR
    public function getRiwayat(Request $request)
    {
        $user = $request->user();

        $data = DB::table('jurnals')
            ->join('jadwal_pelajaran', 'jurnals.jadwal_id', '=', 'jadwal_pelajaran.id')
            ->join('kelas', 'jadwal_pelajaran.kelas_id', '=', 'kelas.id')
            ->join('mata_pelajaran', 'jadwal_pelajaran.mapel_id', '=', 'mata_pelajaran.id')
            ->where('jadwal_pelajaran.guru_id', $user->id)
            ->select(
                'jurnals.id',
                'jurnals.tanggal',
                'jurnals.materi',
                'jurnals.status_pengisian',
                'kelas.nama_kelas',
                'mata_pelajaran.nama_mapel',
                'jadwal_pelajaran.jam_mulai',
                'jadwal_pelajaran.jam_selesai',
                // Hitung jumlah siswa per status menggunakan Subquery COUNT
                DB::raw('(SELECT COUNT(*) FROM presensi_detail WHERE presensi_detail.jurnal_id = jurnals.id AND status = "Hadir") as hadir'),
                DB::raw('(SELECT COUNT(*) FROM presensi_detail WHERE presensi_detail.jurnal_id = jurnals.id AND status = "Sakit") as sakit'),
                DB::raw('(SELECT COUNT(*) FROM presensi_detail WHERE presensi_detail.jurnal_id = jurnals.id AND status = "Izin") as izin'),
                DB::raw('(SELECT COUNT(*) FROM presensi_detail WHERE presensi_detail.jurnal_id = jurnals.id AND status = "Alpha") as alpha')
            )
            ->orderBy('jurnals.tanggal', 'desc') // Paling baru diatas
            ->orderBy('jadwal_pelajaran.jam_mulai', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}