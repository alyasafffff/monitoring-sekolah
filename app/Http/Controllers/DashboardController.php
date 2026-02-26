<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1. Logika Jika User adalah Admin
        if ($user->role === 'admin') {
            
            // Mengambil angka statistik asli dari database
            $totalSiswa = DB::table('siswa')->count();
            $totalGuru   = DB::table('users')->where('role', 'guru')->count();
            $totalKelas  = DB::table('kelas')->count();

            // Mengambil 5 data izin terbaru untuk tabel aktivitas
            $izinTerbaru = DB::table('izin_siswa')
                ->join('siswa', 'izin_siswa.siswa_id', '=', 'siswa.id')
                ->join('kelas', 'siswa.kelas_id', '=', 'kelas.id')
                ->select('izin_siswa.*', 'siswa.nama_siswa', 'kelas.nama_kelas')
                ->orderBy('izin_siswa.created_at', 'desc')
                ->limit(5)
                ->get();

            // Mengirim data ke view dashboard.admin
            return view('dashboard.admin', compact(
                'user', 
                'totalSiswa', 
                'totalGuru', 
                'totalKelas', 
                'izinTerbaru'
            ));
        } 

        // 2. Jalur untuk Role lain (sementara biarkan dulu)
        if ($user->role === 'bk') {
            return view('dashboard.bk', compact('user'));
        } 

        if ($user->role === 'kepsek') {
            return view('dashboard.kepsek', compact('user'));
        }

        return view('dashboard.guru_denied'); 
    }
}