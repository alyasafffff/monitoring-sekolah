<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\IzinSiswa; // <--- PENTING: Import Modelnya

class IzinController extends Controller
{
    // ========================================================================
    // 1. API AMBIL DAFTAR SISWA (GET)
    // ========================================================================
    public function getSiswa(Request $request)
    {
        $user = $request->user();

        $kelas = DB::table('kelas')->where('wali_kelas_id', $user->id)->first();

        if (!$kelas) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak terdaftar sebagai Wali Kelas.',
                'data' => []
            ], 404);
        }

        $siswa = DB::table('siswa')
            ->where('kelas_id', $kelas->id)
            ->select('id', 'nama_siswa', 'nisn')
            ->orderBy('nama_siswa', 'asc')
            ->get();

        if ($siswa->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Belum ada data siswa di kelas ' . $kelas->nama_kelas,
                'data' => []
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data siswa berhasil dimuat',
            'nama_kelas' => $kelas->nama_kelas,
            'data' => $siswa
        ], 200);
    }

    // ========================================================================
    // 2. API INPUT IZIN (POST) - UPDATE PAKAI MODEL & KETERANGAN
    // ========================================================================
    public function inputIzin(Request $request)
    {
        // A. Validasi (Ubah format jam_mulai jadi integer/jam_ke)
        $request->validate([
            'siswa_id' => 'required|integer',
            'status' => 'required|in:Sakit,Izin',
            'jenis_izin' => 'required|in:full,jam',
            'keterangan' => 'nullable|string',

            // SEKARANG PAKAI INTEGER (Jam Ke-1, Jam Ke-2, dst)
            'jam_ke_mulai' => 'nullable|integer|min:1',
            'jam_ke_selesai' => 'nullable|integer|min:1|after_or_equal:jam_ke_mulai',

            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        $guru_login_id = $request->user()->id;

        // B. Cek Hak Akses Wali Kelas (Tetap sama)
        $siswa = DB::table('siswa')->where('id', $request->siswa_id)->first();
        if (!$siswa) return response()->json(['success' => false, 'message' => 'Siswa tidak ditemukan.'], 404);

        $kelas = DB::table('kelas')->where('id', $siswa->kelas_id)->first();
        if ($kelas->wali_kelas_id !== $guru_login_id) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        // C. Siapkan Data Jam (Ganti variabel jam jadi jam_ke)
        $jamKeMulai = null;
        $jamKeSelesai = null;
        if ($request->jenis_izin == 'jam') {
            if (!$request->jam_ke_mulai || !$request->jam_ke_selesai) {
                return response()->json(['success' => false, 'message' => 'Sesi jam wajib diisi untuk izin parsial.'], 400);
            }
            $jamKeMulai = $request->jam_ke_mulai;
            $jamKeSelesai = $request->jam_ke_selesai;
        }

        // D. LOOPING TANGGAL (Pakai Model IzinSiswa)
        $startDate = Carbon::parse($request->tanggal_mulai);
        $endDate = Carbon::parse($request->tanggal_selesai);

        $suksesCount = 0;

        while ($startDate->lte($endDate)) {
            $tanggalSaatIni = $startDate->format('Y-m-d');

            // 1. Cek Dobel
            $cek = IzinSiswa::where('siswa_id', $request->siswa_id)
                ->where('tanggal_izin', $tanggalSaatIni)
                ->exists();

            // 2. Simpan Pakai Nama Kolom Baru (jam_ke_mulai & jam_ke_selesai)
            if (!$cek) {
                IzinSiswa::create([
                    'siswa_id'      => $request->siswa_id,
                    'wali_kelas_id' => $guru_login_id,
                    'tanggal_izin'  => $tanggalSaatIni,
                    'status'        => $request->status,
                    'keterangan'    => $request->keterangan,
                    'jam_ke_mulai'  => $jamKeMulai, // Hasil input integer
                    'jam_ke_selesai' => $jamKeSelesai, // Hasil input integer
                ]);
                $suksesCount++;
            }

            $startDate->addDay();
        }

        if ($suksesCount == 0) {
            return response()->json(['success' => false, 'message' => 'Siswa sudah tercatat izin pada tanggal tersebut.'], 400);
        }

        return response()->json(['success' => true, 'message' => "Berhasil mencatat izin untuk $suksesCount hari."], 200);
    }
    public function getRiwayatIzin(Request $request)
{
    $user = $request->user(); 

    try {
        // 1. Cari dulu ID Kelas yang dipegang oleh Guru ini
        $kelas = DB::table('kelas')->where('wali_kelas_id', $user->id)->first();

        if (!$kelas) {
            return response()->json([
                'success' => false,
                'message' => 'Data kelas tidak ditemukan untuk wali kelas ini.'
            ], 404);
        }

        // 2. Ambil riwayat izin berdasarkan kelas tersebut
        $riwayat = DB::table('izin_siswa')
            ->join('siswa', 'izin_siswa.siswa_id', '=', 'siswa.id')
            ->where('siswa.kelas_id', $kelas->id) // Filter pakai ID Kelas yang ketemu tadi
            ->select(
                'izin_siswa.id',
                'siswa.nama_siswa', // Sesuaikan dengan kolom 'nama_siswa' di tabel siswa kamu
                'izin_siswa.status',
                // 'jenis_izin' mungkin tidak ada di DB jika kamu hanya simpan per tanggal tunggal
                DB::raw("IF(izin_siswa.jam_ke_mulai IS NULL, 'full', 'jam') as jenis_izin"), 
                'izin_siswa.tanggal_izin as tanggal_mulai', // Aliasing agar sesuai dengan Model di Flutter
                'izin_siswa.tanggal_izin as tanggal_selesai',
                'izin_siswa.jam_ke_mulai',
                'izin_siswa.jam_ke_selesai',
                'izin_siswa.keterangan'
            )
            ->orderBy('izin_siswa.tanggal_izin', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar riwayat izin berhasil diambil',
            'data' => $riwayat
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            // Baris ini akan mengirim pesan error asli ke Flutter agar kamu bisa baca di debug console
            'message' => 'Terjadi kesalahan: ' . $e->getMessage() 
        ], 500);
    }
}
}
