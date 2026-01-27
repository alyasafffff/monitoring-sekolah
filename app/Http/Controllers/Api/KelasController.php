<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    // 1. GET ALL (Sekarang muncul nama Gurunya juga!)
    public function index()
    {
        // with('waliKelas') --> Teknik "Eager Loading" biar Laravel otomatis ambil data user terkait
        $kelas = Kelas::with('waliKelas:id,name,nip')->orderBy('nama_kelas', 'asc')->get();
        
        return response()->json([
            'success' => true,
            'data' => $kelas
        ], 200);
    }

    // 2. CREATE
    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required|string|unique:kelas,nama_kelas',
            'wali_kelas_id' => 'nullable|exists:users,id', // Pastikan ID user-nya ada di tabel users
        ]);

        $kelas = Kelas::create([
            'nama_kelas' => $request->nama_kelas,
            'wali_kelas_id' => $request->wali_kelas_id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kelas berhasil ditambahkan',
            'data' => $kelas
        ], 201);
    }

    // 3. SHOW
    public function show($id)
    {
        $kelas = Kelas::with('waliKelas:id,name,nip')->find($id);

        if (!$kelas) {
            return response()->json(['success' => false, 'message' => 'Kelas tidak ditemukan'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $kelas
        ], 200);
    }

    // 4. UPDATE
    public function update(Request $request, $id)
    {
        $kelas = Kelas::find($id);

        if (!$kelas) {
            return response()->json(['success' => false, 'message' => 'Kelas tidak ditemukan'], 404);
        }

        $request->validate([
            'nama_kelas' => 'required|string|unique:kelas,nama_kelas,' . $id,
            'wali_kelas_id' => 'nullable|exists:users,id'
        ]);

        $kelas->update([
            'nama_kelas' => $request->nama_kelas,
            'wali_kelas_id' => $request->wali_kelas_id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kelas berhasil diupdate'
        ], 200);
    }

    // 5. DELETE
    public function destroy($id)
    {
        $kelas = Kelas::find($id);

        if (!$kelas) {
            return response()->json(['success' => false, 'message' => 'Kelas tidak ditemukan'], 404);
        }

        $kelas->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kelas berhasil dihapus'
        ], 200);
    }
}