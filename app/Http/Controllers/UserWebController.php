<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage; // <--- WAJIB TAMBAH INI

class UserWebController extends Controller
{
    // 1. HALAMAN UTAMA
    public function index()
    {
        $petinggi = DB::table('users')
            ->whereIn('role', ['admin', 'kepsek', 'bk'])
            ->orderBy('role', 'asc')
            ->get();

        $guru = DB::table('users')
            ->where('role', 'guru')
            ->orderBy('name', 'asc')
            ->get();

        // [CORRECTED] Mengarah ke folder 'user' (tunggal)
        return view('dashboard.user.index', compact('petinggi', 'guru'));
    }

    // 2. FORM TAMBAH USER
    public function create()
    {
        $sudahAdaKepsek = DB::table('users')->where('role', 'kepsek')->exists();
        // [CORRECTED] Mengarah ke folder 'user'
        return view('dashboard.user.create', compact('sudahAdaKepsek'));
    }

    // 3. PROSES SIMPAN (+ LOGIKA UPLOAD FOTO)
    public function store(Request $request)
    {
        $request->validate([
            'nip' => 'required|unique:users,nip',
            'name' => 'required|string',
            'role' => 'required|in:admin,bk,kepsek,guru',
            'password' => 'required|min:6',
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Validasi Foto
        ]);

        // A. Proses Upload Foto (Jika ada)
        $pathFoto = null;
        if ($request->hasFile('foto_profil')) {
            // Simpan ke folder: storage/app/public/profile_photos
            $pathFoto = $request->file('foto_profil')->store('profile_photos', 'public');
        }

        // B. Simpan ke Database
        DB::table('users')->insert([
            'nip' => $request->nip,
            'name' => $request->name,
            'role' => $request->role,
            'password' => Hash::make($request->password),
            'no_hp' => $request->no_hp,
            'foto_profil' => $pathFoto, // <--- Simpan Path di sini
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
        // [CORRECTED] Mengarah ke folder 'user'
        return view('dashboard.user.edit', compact('user'));
    }

    // 5. PROSES UPDATE (+ LOGIKA GANTI FOTO)
    public function update(Request $request, $id)
    {
        $request->validate([
            'nip' => 'required|unique:users,nip,' . $id,
            'name' => 'required|string',
            'role' => 'required|in:admin,bk,kepsek,guru',
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Data dasar
        $data = [
            'nip' => $request->nip,
            'name' => $request->name,
            'role' => $request->role,
            'no_hp' => $request->no_hp,
            'is_active' => $request->has('is_active'),
            'updated_at' => now(),
        ];

        // Jika password diisi, update password
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // LOGIKA GANTI FOTO
        if ($request->hasFile('foto_profil')) {
            // 1. Hapus foto lama biar server gak penuh
            $userLama = DB::table('users')->where('id', $id)->first();
            if ($userLama->foto_profil && Storage::disk('public')->exists($userLama->foto_profil)) {
                Storage::disk('public')->delete($userLama->foto_profil);
            }

            // 2. Upload foto baru
            $pathFoto = $request->file('foto_profil')->store('profile_photos', 'public');
            $data['foto_profil'] = $pathFoto;
        }

        DB::table('users')->where('id', $id)->update($data);

        return redirect()->route('users.index')->with('success', 'Data User Berhasil Diupdate!');
    }

    // 6. HAPUS USER (+ HAPUS FILE FOTO)
    public function destroy($id)
    {
        if (Auth::id() == $id) {
            return back()->with('error', 'Anda tidak bisa menghapus akun sendiri!');
        }

        // Cek Wali Kelas
        $cekWali = DB::table('kelas')->where('wali_kelas_id', $id)->exists();
        if ($cekWali) {
            return back()->with('error', 'Gagal! User ini terdaftar sebagai Wali Kelas.');
        }

        // Ambil data user untuk cek foto
        $user = DB::table('users')->where('id', $id)->first();

        // Hapus file foto dari folder storage
        if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
            Storage::disk('public')->delete($user->foto_profil);
        }

        // Hapus data DB
        DB::table('users')->where('id', $id)->delete();

        return redirect()->route('users.index')->with('success', 'User Berhasil Dihapus!');
    }
}