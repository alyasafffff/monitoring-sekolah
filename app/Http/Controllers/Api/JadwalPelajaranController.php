<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JadwalPelajaran;
use Illuminate\Http\Request;

class JadwalPelajaranController extends Controller
{
    // 1. GET ALL
    public function index()
    {
        // Ambil jadwal beserta nama guru, kelas, dan mapelnya
        $jadwal = JadwalPelajaran::with(['guru', 'kelas', 'mapel'])->get();
        return response()->json(['success' => true, 'data' => $jadwal]);
    }

    // 2. CREATE
    public function store(Request $request)
    {
        $request->validate([
            'guru_id'     => 'required|exists:users,id',
            'kelas_id'    => 'required|exists:kelas,id',
            'mapel_id'    => 'required|exists:mata_pelajaran,id', // Cek nama tabel mapel di DB
            'hari'        => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_mulai'   => 'required',
            'jam_selesai' => 'required',
        ]);

        $jadwal = JadwalPelajaran::create($request->all());

        return response()->json(['success' => true, 'message' => 'Jadwal berhasil dibuat!', 'data' => $jadwal], 201);
    }

    // 3. SHOW (Detail 1 Jadwal)
    public function show($id)
    {
        $jadwal = JadwalPelajaran::with(['guru', 'kelas', 'mapel'])->find($id);
        
        if (!$jadwal) {
            return response()->json(['success' => false, 'message' => 'Jadwal tidak ditemukan'], 404);
        }

        return response()->json(['success' => true, 'data' => $jadwal]);
    }

    // 4. UPDATE
    public function update(Request $request, $id)
    {
        $jadwal = JadwalPelajaran::find($id);
        if (!$jadwal) return response()->json(['success' => false, 'message' => 'Jadwal tidak ditemukan'], 404);

        $jadwal->update($request->all());

        return response()->json(['success' => true, 'message' => 'Jadwal berhasil diupdate!', 'data' => $jadwal]);
    }

    // 5. DELETE
    public function destroy($id)
    {
        $jadwal = JadwalPelajaran::find($id);
        if (!$jadwal) return response()->json(['success' => false, 'message' => 'Jadwal tidak ditemukan'], 404);

        $jadwal->delete();
        return response()->json(['success' => true, 'message' => 'Jadwal berhasil dihapus!']);
    }
}