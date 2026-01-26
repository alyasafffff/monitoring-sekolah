<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // 1. Validasi Inputan dari Flutter
        $request->validate([
            'nip' => 'required|string',
            'password' => 'required|string',
        ]);

        // 2. Cari User berdasarkan NIP
        $user = User::where('nip', $request->nip)->first();

        // 3. Cek apakah User ada & Password benar
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'NIP atau Password salah.'
            ], 401);
        }

        // ==========================================
        // 4. CEK KHUSUS: Apakah Guru ini Wali Kelas?
        // ==========================================
        $is_walikelas = false;
        $kelas_id = null;
        $nama_kelas = null;

        if ($user->role === 'guru') {
            // Cek di tabel kelas, adakah kelas yang wali_kelas_id-nya = ID guru ini?
            $kelas = DB::table('kelas')->where('wali_kelas_id', $user->id)->first();
            
            if ($kelas) {
                $is_walikelas = true;
                $kelas_id = $kelas->id;
                $nama_kelas = $kelas->nama_kelas;
            }
        }

        // 5. Buat Kunci Digital (Token) menggunakan Laravel Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        // 6. Kirim Balasan (Response) ke Aplikasi Flutter
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
                'is_walikelas' => $is_walikelas, // True/False untuk trigger Tab di Flutter
                'kelas_id' => $kelas_id,
                'nama_kelas' => $nama_kelas
            ]
        ], 200);
    }
}