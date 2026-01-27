<?php

namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class SiswaController extends Controller
{
    // Fungsi untuk mengambil data siswa
    // Fungsi untuk mengambil data siswa
    public function index()
    {
        // Ambil data siswa DENGAN IDENTITAS LENGKAP
        $siswa = DB::table('siswa')
                    ->join('kelas', 'siswa.kelas_id', '=', 'kelas.id')
                    ->select(
                        'siswa.id', 
                        'siswa.nisn', 
                        'siswa.nama_siswa', 
                        'kelas.nama_kelas', 
                        'siswa.jenis_kelamin',  // <--- Tambahan Baru
                        'siswa.tanggal_lahir',  // <--- Tambahan Baru
                        'siswa.alamat',         // <--- Tambahan Baru
                        'siswa.no_hp_ortu',     // <--- Tambahan Baru
                        'siswa.qr_token'
                    )
                    ->orderBy('kelas.nama_kelas', 'asc')
                    ->orderBy('siswa.nama_siswa', 'asc')
                    ->get();

        // Kembalikan dalam bentuk JSON
        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil daftar siswa lengkap',
            'data' => $siswa
        ], 200);
    }
    // Fungsi untuk Menambah Siswa Baru (Create)
 public function store(Request $request)
    {
        // 1. Validasi inputan dari TU (Sekarang lebih lengkap!)
        $request->validate([
            'nisn' => 'required|unique:siswa,nisn',
            'nama_siswa' => 'required|string',
            'kelas_id' => 'required|exists:kelas,id',
            // Validasi data tambahan:
            'jenis_kelamin' => 'required|in:L,P',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',
            'no_hp_ortu' => 'nullable|string|max:15'
        ]);

        // 2. Buat QR Token Otomatis
        $nama_depan = explode(' ', trim($request->nama_siswa))[0];
        $token_otomatis = 'SMPN2-' . $nama_depan . '-' . \Illuminate\Support\Str::random(6);

        // 3. Simpan SEMUA data ke Database
        $id_baru = DB::table('siswa')->insertGetId([
            'nisn' => $request->nisn,
            'nama_siswa' => $request->nama_siswa,
            'kelas_id' => $request->kelas_id,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tanggal_lahir' => $request->tanggal_lahir,
            'alamat' => $request->alamat,
            'no_hp_ortu' => $request->no_hp_ortu,
            'qr_token' => $token_otomatis,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Siswa lengkap berhasil ditambahkan & QR Code dibuat!',
            'data' => [
                'id' => $id_baru,
                'nama_siswa' => $request->nama_siswa,
                'qr_token' => $token_otomatis
            ]
        ], 201);
    }
    // 3. Fungsi untuk MENGUBAH Data Siswa (Update)
    public function update(Request $request, $id)
    {
        // Cek dulu apakah siswanya ada?
        $siswa = DB::table('siswa')->where('id', $id)->first();
        if (!$siswa) {
            return response()->json(['success' => false, 'message' => 'Siswa tidak ditemukan'], 404);
        }

        // Validasi inputan TU
        $request->validate([
            // Khusus NISN: Boleh sama dengan NISN lama milik diri sendiri
            'nisn' => 'required|unique:siswa,nisn,' . $id, 
            'nama_siswa' => 'required|string',
            'kelas_id' => 'required|exists:kelas,id',
            'jenis_kelamin' => 'required|in:L,P',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',
            'no_hp_ortu' => 'nullable|string|max:15'
        ]);

        // Simpan perubahan ke Database
        DB::table('siswa')->where('id', $id)->update([
            'nisn' => $request->nisn,
            'nama_siswa' => $request->nama_siswa,
            'kelas_id' => $request->kelas_id,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tanggal_lahir' => $request->tanggal_lahir,
            'alamat' => $request->alamat,
            'no_hp_ortu' => $request->no_hp_ortu,
            'updated_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data siswa ' . $request->nama_siswa . ' berhasil diperbarui!'
        ], 200);
    }

    // 4. Fungsi untuk MENGHAPUS Siswa (Delete)
    public function destroy($id)
    {
        // Cek apakah siswa ada
        $siswa = DB::table('siswa')->where('id', $id)->first();
        if (!$siswa) {
            return response()->json(['success' => false, 'message' => 'Siswa tidak ditemukan'], 404);
        }

        // Hapus dari database
        DB::table('siswa')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Siswa bernama ' . $siswa->nama_siswa . ' berhasil dihapus dari sistem!'
        ], 200);
    }
}