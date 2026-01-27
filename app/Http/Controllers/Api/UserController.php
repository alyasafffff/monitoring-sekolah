<?php

namespace App\Http\Controllers\Api;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash; // Penting untuk menyembunyikan password!

class UserController extends Controller
{
    // 1. GET: Melihat Semua User (Admin, Guru, BK, Kepsek)
    public function index()
    {
        // Sengaja kita tidak memunculkan password di hasil pencarian demi keamanan
        $users = DB::table('users')
                    ->select('id', 'nip', 'name', 'role', 'created_at')
                    ->orderBy('role', 'asc')
                    ->get();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil daftar pengguna',
            'data' => $users
        ], 200);
    }

    // 2. POST: Menambah User Baru
    public function store(Request $request)
    {
        // 1. Validasi
        $request->validate([
            'nip' => 'required|string|max:20|unique:users',
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,guru,bk,kepsek',
            'no_hp' => 'nullable|string|max:15',
            // Validasi Foto: Harus gambar (jpg/png) dan maks 2MB
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', 
        ]);

        // 2. Proses Upload Foto (Jika ada yang dikirim)
        $namaFoto = null;
        if ($request->hasFile('foto_profil')) {
            $file = $request->file('foto_profil');
            $namaFoto = $file->hashName(); // Buat nama acak (misal: a8s7d8.jpg)
            $file->storeAs('public/profil', $namaFoto); // Simpan ke folder storage
        }

        // 3. Simpan ke Database
        $user = DB::table('users')->insertGetId([
            'nip' => $request->nip,
            'name' => $request->name,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'no_hp' => $request->no_hp,      // <--- Masuk database
            'foto_profil' => $namaFoto,      // <--- Masuk database (cuma namanya)
            'is_active' => true,             // <--- Default aktif
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengguna berhasil ditambahkan!',
            'data_id' => $user
        ], 201);
    }

    // 3. PUT: Mengubah Data User
    // 3. PUT: Mengubah Data User
    public function update(Request $request, $id)
    {
        // Cek apakah user ada?
        $user = DB::table('users')->where('id', $id)->first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User tidak ditemukan'], 404);
        }

        // Validasi
        $request->validate([
            // unique:users,nip,ID -> Artinya cek unik, tapi KECUALI id yang sedang diedit ini
            'nip' => 'required|string|max:20|unique:users,nip,' . $id,
            'name' => 'required|string|max:255',
            'role' => 'required|in:admin,guru,bk,kepsek',
            'password' => 'nullable|string|min:6', // Nullable = Boleh kosong
            'no_hp' => 'nullable|string|max:15',
            'is_active' => 'boolean' // true = 1, false = 0
        ]);

        // Siapkan data yang mau diubah
        $dataUpdate = [
            'nip' => $request->nip,
            'name' => $request->name,
            'role' => $request->role,
            'no_hp' => $request->no_hp,
            // Kalau is_active tidak dikirim, pakai data lama ($user->is_active)
            'is_active' => $request->input('is_active', $user->is_active), 
            'updated_at' => now()
        ];

        // Jika password diisi, enkripsi dan masukkan ke data update
        if ($request->filled('password')) {
            $dataUpdate['password'] = Hash::make($request->password);
        }
        
        // (Logika update foto kita skip dulu sesuai request)

        DB::table('users')->where('id', $id)->update($dataUpdate);

        return response()->json([
            'success' => true,
            'message' => 'Data pengguna berhasil diperbarui!'
        ], 200);
    }

    // 4. DELETE: Menghapus User
    // 4. DELETE: Menghapus User & Fotonya
    public function destroy($id)
    {
        // Cari user dulu (kita butuh nama file fotonya)
        $user = DB::table('users')->where('id', $id)->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User tidak ditemukan'], 404);
        }

        // Hapus Foto dari folder 'storage' jika ada
        // (Biar server gak penuh sampah file)
        if ($user->foto_profil) {
            Storage::delete('public/profil/' . $user->foto_profil);
        }

        // Baru hapus datanya dari Database
        DB::table('users')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'User berhasil dihapus dari sistem!'
        ], 200);
    }
}