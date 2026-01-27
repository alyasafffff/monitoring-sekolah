<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JadwalPelajaran;
use Illuminate\Http\Request;

class JadwalPelajaranController extends Controller
{
    // 1. GET ALL (Tampilkan Jadwal Lengkap)
    public function index()
    {
        // with(...) adalah "Eager Loading" biar query database gak berat
        $jadwal = JadwalPelajaran::with(['mapel', 'kelas', 'guru:id,name,nip'])
                    ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")
                    ->orderBy('jam_mulai', 'asc')
                    ->get();
        
        return response()->json([
            'success' => true,
            'data' => $jadwal
        ], 200);
    }

    // 2. CREATE (Tambah Jadwal)
    public function store(Request $request)
    {
        $request->validate([
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'mapel_id' => 'required|exists:mata_pelajaran,id',
            'kelas_id' => 'required|exists:kelas,id',
            'guru_id' => 'required|exists:users,id',
        ]);

        $jadwal = JadwalPelajaran::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Jadwal berhasil dibuat!',
            'data' => $jadwal
        ], 201);
    }

    // 3. SHOW (Detail Satu Jadwal)
    public function show($id)
    {
        $jadwal = JadwalPelajaran::with(['mapel', 'kelas', 'guru'])->find($id);

        if (!$jadwal) {
            return response()->json(['success' => false, 'message' => 'Jadwal tidak ditemukan'], 404);
        }

        return response()->json(['success' => true, 'data' => $jadwal], 200);
    }

    // 4. UPDATE (Edit Jadwal)
    public function update(Request $request, $id)
    {
        $jadwal = JadwalPelajaran::find($id);

        if (!$jadwal) {
            return response()->json(['success' => false, 'message' => 'Jadwal tidak ditemukan'], 404);
        }

        $request->validate([
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'mapel_id' => 'required|exists:mata_pelajaran,id',
            'kelas_id' => 'required|exists:kelas,id',
            'guru_id' => 'required|exists:users,id',
        ]);

        $jadwal->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Jadwal berhasil diperbarui!'
        ], 200);
    }

    // 5. DELETE (Hapus Jadwal)
    public function destroy($id)
    {
        $jadwal = JadwalPelajaran::find($id);

        if (!$jadwal) {
            return response()->json(['success' => false, 'message' => 'Jadwal tidak ditemukan'], 404);
        }

        $jadwal->delete();

        return response()->json([
            'success' => true,
            'message' => 'Jadwal berhasil dihapus'
        ], 200);
    }
}