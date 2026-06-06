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
        // A. Validasi (Tambahkan 'foto' di sini)
        $request->validate([
            'siswa_id' => 'required|integer',
            'status' => 'required|in:Sakit,Izin',
            'jenis_izin' => 'required|in:full,jam',
            'keterangan' => 'nullable|string',

            // Validasi Foto: Maksimal 2MB, format gambar
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

            'jam_ke_mulai' => 'nullable|integer|min:1',
            'jam_ke_selesai' => 'nullable|integer|min:1',

            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        $guru_login_id = $request->user()->id;

        // B. Cek Hak Akses (Tetap sama seperti sebelumnya)
        $siswa = DB::table('siswa')->where('id', $request->siswa_id)->first();
        if (!$siswa) return response()->json(['success' => false, 'message' => 'Siswa tidak ditemukan.'], 404);

        $kelas = DB::table('kelas')->where('id', $siswa->kelas_id)->first();
        if ($kelas->wali_kelas_id !== $guru_login_id) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        // C. Proses Upload Foto (Jika ada)
        $namaFileFoto = null;
        if ($request->hasFile('foto')) {
            // Kita simpan di folder 'public/bukti_izin'
            // store() akan otomatis generate nama unik buat filenya
            $path = $request->file('foto')->store('bukti_izin', 'public');
            $namaFileFoto = $path;
        }

        // D. Siapkan Data Jam
        $jamKeMulai = ($request->jenis_izin == 'jam') ? $request->jam_ke_mulai : null;
        $jamKeSelesai = ($request->jenis_izin == 'jam') ? $request->jam_ke_selesai : null;

        // E. LOOPING TANGGAL
        $startDate = Carbon::parse($request->tanggal_mulai);
        $endDate = Carbon::parse($request->tanggal_selesai);
        $suksesCount = 0;

        while ($startDate->lte($endDate)) {
            $tanggalSaatIni = $startDate->format('Y-m-d');

            $cek = IzinSiswa::where('siswa_id', $request->siswa_id)
                ->where('tanggal_izin', $tanggalSaatIni)
                ->exists();

            if (!$cek) {
                IzinSiswa::create([
                    'siswa_id'      => $request->siswa_id,
                    'wali_kelas_id' => $guru_login_id,
                    'tanggal_izin'  => $tanggalSaatIni,
                    'status'        => $request->status,
                    'keterangan'    => $request->keterangan,
                    'bukti_foto'    => $namaFileFoto, // Simpan path fotonya
                    'jam_ke_mulai'  => $jamKeMulai,
                    'jam_ke_selesai' => $jamKeSelesai,
                ]);
                $suksesCount++;
            }
            $startDate->addDay();
        }

        if ($suksesCount == 0) {
            return response()->json(['success' => false, 'message' => 'Siswa sudah tercatat izin pada tanggal tersebut.'], 400);
        }

        return response()->json(['success' => true, 'message' => "Berhasil mencatat izin untuk $suksesCount hari."], 200);
    }
    public function getRiwayatIzin(Request $request)
    {
        $user = $request->user();

        try {
            $kelas = DB::table('kelas')->where('wali_kelas_id', $user->id)->first();

            if (!$kelas) {
                return response()->json(['success' => false, 'message' => 'Data kelas tidak ditemukan.'], 404);
            }

            $riwayat = DB::table('izin_siswa')
                ->join('siswa', 'izin_siswa.siswa_id', '=', 'siswa.id')
                ->where('siswa.kelas_id', $kelas->id)
                ->select(
                    DB::raw("MAX(izin_siswa.id) as id"), // Gunakan MAX agar ID tidak rancu saat grouping
                    'siswa.nama_siswa',
                    'izin_siswa.status',
                    'izin_siswa.keterangan',
                    'izin_siswa.bukti_foto',
                    'izin_siswa.jam_ke_mulai',
                    'izin_siswa.jam_ke_selesai',
                    DB::raw("MIN(izin_siswa.tanggal_izin) as tanggal_mulai"),
                    DB::raw("MAX(izin_siswa.tanggal_izin) as tanggal_selesai"),
                    DB::raw("COUNT(*) as total_hari")
                )
                ->groupBy(
                    'siswa.nama_siswa',
                    'izin_siswa.status',
                    'izin_siswa.keterangan',
                    'izin_siswa.bukti_foto',
                    'izin_siswa.jam_ke_mulai',
                    'izin_siswa.jam_ke_selesai'
                )
                ->orderBy('tanggal_mulai', 'desc')
                ->get()
                ->map(function ($item) {
                    // Tambahkan URL foto secara dinamis setelah get data
                    $item->jenis_izin = is_null($item->jam_ke_mulai) ? 'full' : 'jam';
                    $item->url_foto = $item->bukti_foto ? url('storage/' . $item->bukti_foto) : null;
                    return $item;
                });

            return response()->json([
                'success' => true,
                'data' => $riwayat
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
