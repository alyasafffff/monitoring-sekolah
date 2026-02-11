<?php

namespace App\Http\Controllers\Api;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    // 1. GET PROFILE
    public function getProfile(Request $request)
    {
        $user = $request->user();

        // Logika foto (tetap)
        $fotoUrl = null;
        if ($user->foto_profil) { 
            $fotoUrl = asset('storage/' . $user->foto_profil);
        }

        // Statistik (tetap)
        $totalSesi = DB::table('jurnals')
            ->join('jadwal_pelajaran', 'jurnals.jadwal_id', '=', 'jadwal_pelajaran.id')
            ->where('jadwal_pelajaran.guru_id', $user->id)
            ->count();

        $kelasDiajar = DB::table('jadwal_pelajaran')
            ->where('guru_id', $user->id)
            ->distinct()->pluck('kelas_id');
            
        $totalSiswa = DB::table('siswa')
            ->whereIn('kelas_id', $kelasDiajar)
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'nama' => $user->name,
                'nip'  => $user->nip ?? '-',
                'role' => 'Guru Mata Pelajaran',
                'foto_url' => $fotoUrl,
                
                // --- TAMBAHAN PENTING ---
                'no_hp' => $user->no_hp, // <-- Kirim data HP agar bisa diambil Android
                // 'email' => $user->email, // (Uncomment jika di tabel users ada kolom email)
                // 'alamat' => $user->alamat, // (Uncomment jika di tabel users ada kolom alamat)
                
                'stats' => [
                    'total_sesi' => $totalSesi,
                    'total_siswa' => $totalSiswa
                ]
            ]
        ]);
    }
    // 2. UPDATE FOTO
    public function updateFoto(Request $request)
    {
        // Validasi input dari Flutter tetap bernama 'foto'
        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = $request->user();

        if ($request->hasFile('foto')) {
            // A. Hapus foto lama (Cek kolom foto_profil)
            if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
                Storage::disk('public')->delete($user->foto_profil);
            }

            // B. Simpan file fisik
            $path = $request->file('foto')->store('profile_photos', 'public');

            // C. Simpan path ke Database (Kolom foto_profil)
            $user->foto_profil = $path; // <--- UBAH DI SINI
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Foto berhasil diupdate',
                'foto_url' => asset('storage/' . $path), 
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Gagal upload'], 400);
    }
    // ... (Fungsi getProfile dan updateFoto biarkan saja di atas) ...

    // 3. UPDATE DATA DIRI (No HP, Email, Alamat)
    public function updateData(Request $request)
    {
        // Validasi input
        $request->validate([
            'no_hp' => 'nullable|string|max:15',
            // Tambahkan validasi lain jika kolom email/alamat sudah ada
            // 'email' => 'nullable|email',
            // 'alamat' => 'nullable|string',
        ]);

        $user = $request->user();

        // Cek data apa saja yang dikirim dari Flutter
        if ($request->has('no_hp')) {
            $user->no_hp = $request->no_hp;
        }

        // Uncomment jika database sudah ada kolom ini
        // if ($request->has('email')) { $user->email = $request->email; }
        // if ($request->has('alamat')) { $user->alamat = $request->alamat; }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui',
            'data' => $user
        ]);
    }
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|regex:/[0-9]/|confirmed', 
            // 'confirmed' artinya Laravel akan mencari field bernama 'new_password_confirmation'
        ]);

        $user = $request->user();

        // 1. Cek apakah password lama benar?
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Password saat ini salah.',
                'errors' => ['current_password' => ['Password saat ini tidak sesuai']]
            ], 422);
        }

        // 2. Update Password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diperbarui.'
        ]);
    }

} 