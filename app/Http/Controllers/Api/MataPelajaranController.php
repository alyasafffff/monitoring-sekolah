<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;

class MataPelajaranController extends Controller
{
    // 1. GET ALL
    public function index()
    {
        $mapel = MataPelajaran::orderBy('nama_mapel', 'asc')->get();
        
        return response()->json([
            'success' => true,
            'data' => $mapel
        ], 200);
    }

    // 2. CREATE
    public function store(Request $request)
    {
        // Validasi Kode Mapel & Nama Mapel
        $request->validate([
            'kode_mapel' => 'required|string|max:10|unique:mata_pelajaran,kode_mapel',
            'nama_mapel' => 'required|string|max:100'
        ]);

        $mapel = MataPelajaran::create([
            'kode_mapel' => $request->kode_mapel,
            'nama_mapel' => $request->nama_mapel
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mata pelajaran berhasil ditambahkan',
            'data' => $mapel
        ], 201);
    }

    // 3. SHOW
    public function show($id)
    {
        $mapel = MataPelajaran::find($id);

        if (!$mapel) {
            return response()->json(['success' => false, 'message' => 'Mapel tidak ditemukan'], 404);
        }

        return response()->json(['success' => true, 'data' => $mapel], 200);
    }

    // 4. UPDATE
    public function update(Request $request, $id)
    {
        $mapel = MataPelajaran::find($id);

        if (!$mapel) {
            return response()->json(['success' => false, 'message' => 'Mapel tidak ditemukan'], 404);
        }

        $request->validate([
            // unique:tabel,kolom,kecuali_id_ini
            'kode_mapel' => 'required|string|max:10|unique:mata_pelajaran,kode_mapel,' . $id,
            'nama_mapel' => 'required|string|max:100'
        ]);

        $mapel->update([
            'kode_mapel' => $request->kode_mapel,
            'nama_mapel' => $request->nama_mapel
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mata pelajaran berhasil diupdate'
        ], 200);
    }

    // 5. DELETE
    public function destroy($id)
    {
        $mapel = MataPelajaran::find($id);

        if (!$mapel) {
            return response()->json(['success' => false, 'message' => 'Mapel tidak ditemukan'], 404);
        }

        $mapel->delete();

        return response()->json([
            'success' => true,
            'message' => 'Mata pelajaran berhasil dihapus'
        ], 200);
    }
}