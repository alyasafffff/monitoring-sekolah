<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserWebController extends Controller
{
    // 1. HALAMAN UTAMA (Sesuai Request: Atas Petinggi, Bawah Guru)
    public function index()
    {
        // Grup 1: Petinggi (Admin, Kepsek, BK)
        $petinggi = DB::table('users')
            ->whereIn('role', ['admin', 'kepsek', 'bk'])
            ->orderBy('role', 'asc')
            ->get();

        // Grup 2: Guru
        $guru = DB::table('users')
            ->where('role', 'guru')
            ->orderBy('name', 'asc')
            ->get();

        return view('dashboard.user.index', compact('petinggi', 'guru'));
    }

    // 2. FORM TAMBAH USER (Bisa pilih Role)
// 2. FORM TAMBAH USER
    public function create()
    {
        // Cek di database: Apakah sudah ada user dengan role 'kepsek'?
        // Outputnya TRUE jika sudah ada, FALSE jika belum ada
        $sudahAdaKepsek = DB::table('users')->where('role', 'kepsek')->exists();

        // Kirim variabel $sudahAdaKepsek ke View
        return view('dashboard.user.create', compact('sudahAdaKepsek'));
    }

    // 3. PROSES SIMPAN
    public function store(Request $request)
    {
        $request->validate([
            'nip' => 'required|unique:users,nip',
            'name' => 'required|string',
            'role' => 'required|in:admin,bk,kepsek,guru', // Pilihan Role
            'password' => 'required|min:6',
        ]);

        DB::table('users')->insert([
            'nip' => $request->nip,
            'name' => $request->name,
            'role' => $request->role,
            'password' => Hash::make($request->password),
            'no_hp' => $request->no_hp,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('users.index')->with('success', 'User Baru Berhasil Ditambahkan!');
    }

    // 4. FORM EDIT
    public function edit($id)
    {
        $user = DB::table('users')->where('id', $id)->first();
        return view('dashboard.user.edit', compact('user'));
    }

    // 5. PROSES UPDATE
    public function update(Request $request, $id)
    {
        $request->validate([
            'nip' => 'required|unique:users,nip,' . $id,
            'name' => 'required|string',
            'role' => 'required|in:admin,bk,kepsek,guru',
        ]);

        $data = [
            'nip' => $request->nip,
            'name' => $request->name,
            'role' => $request->role,
            'no_hp' => $request->no_hp,
            'is_active' => $request->has('is_active'),
            'updated_at' => now(),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        DB::table('users')->where('id', $id)->update($data);

        return redirect()->route('users.index')->with('success', 'Data User Berhasil Diupdate!');
    }

    // 6. HAPUS USER (Dengan Validasi Keamanan)
    public function destroy($id)
    {
        // PERBAIKAN DISINI: Gunakan Auth::id()
        // Ini artinya: "Ambil ID milik user yang sedang login sekarang"
        if (Auth::id() == $id) {
            return back()->with('error', 'Anda tidak bisa menghapus akun sendiri!');
        }

        // Cek jika Guru, apakah dia Wali Kelas?
        $cekWali = DB::table('kelas')->where('wali_kelas_id', $id)->exists();
        if ($cekWali) {
            return back()->with('error', 'Gagal! User ini terdaftar sebagai Wali Kelas.');
        }

        DB::table('users')->where('id', $id)->delete();
        return redirect()->route('users.index')->with('success', 'User Berhasil Dihapus!');
    }
}