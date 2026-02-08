<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    // 1. LOGIN
    public function login(Request $request)
    {
        // A. Validasi Input
        $request->validate([
            'nip' => 'required|string',
            'password' => 'required|string',
        ]);

        // B. Cari User
        $user = User::where('nip', $request->nip)->first();

        // C. Cek Password
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'NIP atau Password salah.'
            ], 401);
        }

        // D. Cek Status Aktif
        if (!$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda telah dinonaktifkan Admin.'
            ], 403);
        }

        // ============================================================
        // E. CEK ROLE (PERUBAHAN DISINI)
        // ============================================================
        // Jika role user BUKAN 'guru', langsung tolak!
        if ($user->role !== 'guru') {
            return response()->json([
                'success' => false,
                'message' => 'Akses Ditolak. Aplikasi ini khusus untuk Guru.'
            ], 403); 
        }

        // F. CEK KHUSUS: Apakah Guru ini Wali Kelas?
        $is_walikelas = false;
        $kelas_id = null;
        $nama_kelas = null;

        // Kita tidak perlu cek if ($user->role === 'guru') lagi karena sudah disaring di poin E
        $kelas = DB::table('kelas')->where('wali_kelas_id', $user->id)->first();
        
        if ($kelas) {
            $is_walikelas = true;
            $kelas_id = $kelas->id;
            $nama_kelas = $kelas->nama_kelas;
        }

        // G. Buat Token Baru
        $token = $user->createToken('auth_token')->plainTextToken;

        // H. Kirim Respon
        return response()->json([
            'success' => true,
            'message' => 'Login Berhasil',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'nip' => $user->nip,
                'role' => $user->role,
                'photo' => 'https://ui-avatars.com/api/?name='.urlencode($user->name),
                'is_walikelas' => $is_walikelas,
                'kelas_id' => $kelas_id,
                'nama_kelas' => $nama_kelas
            ]
        ], 200);
    }

    // 2. LOGOUT (Wajib ada buat tombol logout di HP)
    public function logout(Request $request)
    {
        // Hapus token yang sedang dipakai
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout Berhasil'
        ]);
    }
}