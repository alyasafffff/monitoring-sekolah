<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
// use App\Models\Jadwal; // Not strictly needed if using DB facade, but okay to keep if used elsewhere

class JadwalController extends Controller
{
    // 1. API UNTUK HALAMAN HOME (GET)
    public function index(Request $request)
    {
        // A. Ambil User Login
        $user = $request->user();

        // B. Set Hari Ini (Format Indo: Senin, Selasa...)
        Carbon::setLocale('id');
        $hariIni = Carbon::now()->isoFormat('dddd');
        $tanggalHariIni = Carbon::now()->format('Y-m-d');

        // C. Query Jadwal (Join Kelas & Mapel)
        $jadwal = DB::table('jadwal_pelajaran')
            ->join('kelas', 'jadwal_pelajaran.kelas_id', '=', 'kelas.id')
            ->join('mata_pelajaran', 'jadwal_pelajaran.mapel_id', '=', 'mata_pelajaran.id')
            ->where('jadwal_pelajaran.guru_id', $user->id)
            ->where('jadwal_pelajaran.hari', $hariIni)
            ->select(
                'jadwal_pelajaran.id', // ID Jadwal
                'kelas.nama_kelas',
                'mata_pelajaran.nama_mapel',
                'jadwal_pelajaran.jam_mulai',
                'jadwal_pelajaran.jam_selesai'
            )
            ->orderBy('jadwal_pelajaran.jam_mulai', 'asc')
            ->get();

        // D. [PENTING] Cek Status Jurnal untuk setiap Jadwal
        $dataLengkap = $jadwal->map(function ($item) use ($tanggalHariIni) {

            // Cek apakah sudah ada Jurnal untuk jadwal ini di tanggal ini?
            $jurnal = DB::table('jurnals')
                ->where('jadwal_id', $item->id)
                ->where('tanggal', $tanggalHariIni)
                ->first();

            if ($jurnal) {
                // Kalau ada, ambil statusnya (proses/selesai) dan ID-nya
                $item->status_jurnal = $jurnal->status_pengisian;
                $item->jurnal_id = $jurnal->id;
            } else {
                // Kalau belum ada, berarti belum dimulai
                $item->status_jurnal = 'belum_mulai';
                $item->jurnal_id = null;
            }

            return $item;
        });

        // E. Kirim ke Flutter
        return response()->json([
            'success' => true,
            'message' => 'List Jadwal Hari Ini',
            'hari' => $hariIni,
            'data' => $dataLengkap
        ], 200);
    }

    public function mulaiKelas(Request $request)
    {
        // KITA BUNGKUS DENGAN TRY-CATCH AGAR KETAHUAN ERRORNYA
        try {
            // A. Validasi
            $request->validate([
                'jadwal_id' => 'required|exists:jadwal_pelajaran,id'
            ]);
    
            $tanggalHariIni = \Carbon\Carbon::now()->format('Y-m-d');
    
            // B. Cek apakah Jurnal sudah ada?
            // (Kode cek jurnal yang sudah ada sebelumnya tetap sama)
            $cekJurnal = DB::table('jurnals')
                ->where('jadwal_id', $request->jadwal_id)
                ->where('tanggal', $tanggalHariIni)
                ->first();
    
            if ($cekJurnal) {
                return response()->json([
                    'success' => true,
                    'message' => 'Kelas sudah dimulai sebelumnya',
                    'data' => ['id' => $cekJurnal->id]
                ], 200);
            }
    
            // C. Buat Jurnal Baru
            $jurnalId = DB::table('jurnals')->insertGetId([
                'jadwal_id' => $request->jadwal_id,
                'tanggal' => $tanggalHariIni,
                'materi' => '-', 
                'status_pengisian' => 'proses',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
    
            // D. GENERATE ABSENSI OTOMATIS
            $jadwal = DB::table('jadwal_pelajaran')->where('id', $request->jadwal_id)->first();
            $siswaKelas = DB::table('siswa')->where('kelas_id', $jadwal->kelas_id)->get();
            $jamMulaiKelas = $jadwal->jam_mulai; 
    
            $dataPresensi = [];
            
            foreach($siswaKelas as $siswa) {
                
                // --- PERUBAHAN UTAMA DISINI ---
                // Default jadi 'Alpha' (Merah/Belum Scan)
                $statusAwal = 'Alpha'; 
    
                // Cek Izin/Sakit dari Wali Kelas
                $izin = DB::table('izin_siswa')
                    ->where('siswa_id', $siswa->id)
                    ->where('tanggal_izin', $tanggalHariIni)
                    ->first();
    
                if ($izin) {
                    // Logika Cek Izin
                    if ($izin->jam_mulai == null) {
                        // Full Day
                        $statusAwal = $izin->status;
                    } else {
                        // Jam Tertentu
                        if ($jamMulaiKelas >= $izin->jam_mulai && $jamMulaiKelas < $izin->jam_selesai) {
                            $statusAwal = $izin->status;
                        }
                    }
                }
    
                $dataPresensi[] = [
                    'jurnal_id' => $jurnalId,
                    'siswa_id' => $siswa->id,
                    'status' => $statusAwal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
    
            if (!empty($dataPresensi)) {
                DB::table('presensi_detail')->insert($dataPresensi);
            }
    
            return response()->json([
                'success' => true,
                'message' => 'Kelas Berhasil Dimulai',
                'data' => ['id' => $jurnalId]
            ], 201);

        } catch (\Exception $e) {
            // INI BAGIAN PENTING: TANGKAP ERROR DAN KIRIM KE HP
            return response()->json([
                'success' => false,
                'message' => 'Server Error: ' . $e->getMessage() . ' (Line: ' . $e->getLine() . ')'
            ], 500);
        }
    }
}