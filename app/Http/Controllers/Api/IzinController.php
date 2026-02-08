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
        // A. Validasi
        $request->validate([
            'siswa_id' => 'required|integer',
            'status' => 'required|in:Sakit,Izin,Dispensasi',
            'jenis_izin' => 'required|in:full,jam',
            // Validasi Keterangan (Boleh kosong/nullable)
            'keterangan' => 'nullable|string', 
            
            // Validasi Jam
            'jam_mulai' => 'nullable|date_format:H:i',
            'jam_selesai' => 'nullable|date_format:H:i|after:jam_mulai',
            
            // Validasi Tanggal
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        $guru_login_id = $request->user()->id;

        // B. Cek Hak Akses Wali Kelas
        $siswa = DB::table('siswa')->where('id', $request->siswa_id)->first();
        if (!$siswa) return response()->json(['success' => false, 'message' => 'Siswa tidak ditemukan.'], 404);
        
        $kelas = DB::table('kelas')->where('id', $siswa->kelas_id)->first();
        if ($kelas->wali_kelas_id !== $guru_login_id) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        // C. Siapkan Data Jam
        $jamMulai = null;
        $jamSelesai = null;
        if ($request->jenis_izin == 'jam') {
            if (!$request->jam_mulai || !$request->jam_selesai) {
                return response()->json(['success' => false, 'message' => 'Jam wajib diisi untuk izin parsial.'], 400);
            }
            $jamMulai = $request->jam_mulai;
            $jamSelesai = $request->jam_selesai;
        }

        // D. LOOPING TANGGAL (Pakai Model IzinSiswa)
        $startDate = Carbon::parse($request->tanggal_mulai);
        $endDate = Carbon::parse($request->tanggal_selesai);
        
        $suksesCount = 0;

        while ($startDate->lte($endDate)) {
            
            $tanggalSaatIni = $startDate->format('Y-m-d');

            // 1. Cek Dobel (Pakai Model juga bisa, tapi query biasa lebih cepat untuk cek)
            $cek = IzinSiswa::where('siswa_id', $request->siswa_id)
                ->where('tanggal_izin', $tanggalSaatIni)
                ->exists(); // Pakai exists() lebih efisien daripada first()

            // 2. Simpan Pakai Model
            if (!$cek) {
                IzinSiswa::create([
                    'siswa_id'      => $request->siswa_id,
                    'wali_kelas_id' => $guru_login_id,
                    'tanggal_izin'  => $tanggalSaatIni,
                    'status'        => $request->status,
                    'keterangan'    => $request->keterangan, // <--- DATA KETERANGAN DISIMPAN
                    'jam_mulai'     => $jamMulai,
                    'jam_selesai'   => $jamSelesai,
                ]);
                $suksesCount++;
            }

            $startDate->addDay();
        }

        if ($suksesCount == 0) {
            return response()->json([
                'success' => false, 
                'message' => 'Siswa sudah tercatat izin pada tanggal tersebut.'
            ], 400);
        }

        return response()->json([
            'success' => true, 
            'message' => "Berhasil mencatat izin untuk $suksesCount hari."
        ], 200);
    }
}