<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MapelWebController extends Controller
{
    // 1. LIHAT DAFTAR MAPEL
    public function index()
    {
        $mapel = DB::table('mata_pelajaran')
            ->orderBy('nama_mapel', 'asc')
            ->get();

        return view('dashboard.mapel.index', compact('mapel'));
    }

    // 2. FORM TAMBAH
    public function create()
    {
        return view('dashboard.mapel.create');
    }

    // 3. SIMPAN DATA
    public function store(Request $request)
    {
        $request->validate([
            'kode_mapel' => 'required|unique:mata_pelajaran,kode_mapel|max:10', // Kode harus unik
            'nama_mapel' => 'required|string|max:255',
        ]);

        DB::table('mata_pelajaran')->insert([
            'kode_mapel' => strtoupper($request->kode_mapel), // Paksa jadi HURUF BESAR
            'nama_mapel' => $request->nama_mapel,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('mapel.index')->with('success', 'Mata Pelajaran Berhasil Ditambahkan!');
    }

    // 4. FORM EDIT
    public function edit($id)
    {
        $mapel = DB::table('mata_pelajaran')->where('id', $id)->first();
        return view('dashboard.mapel.edit', compact('mapel'));
    }

    // 5. UPDATE DATA
    public function update(Request $request, $id)
    {
        $request->validate([
            'kode_mapel' => 'required|max:10|unique:mata_pelajaran,kode_mapel,' . $id, // Unik kecuali punya sendiri
            'nama_mapel' => 'required|string|max:255',
        ]);

        DB::table('mata_pelajaran')->where('id', $id)->update([
            'kode_mapel' => strtoupper($request->kode_mapel),
            'nama_mapel' => $request->nama_mapel,
            'updated_at' => now(),
        ]);

        return redirect()->route('mapel.index')->with('success', 'Mata Pelajaran Berhasil Diupdate!');
    }

    // 6. HAPUS DATA
    public function destroy($id)
    {
        // (Nanti) Cek dulu apakah mapel ini dipakai di Jadwal Pelajaran?
        // Untuk sekarang langsung hapus saja dulu
        
        DB::table('mata_pelajaran')->where('id', $id)->delete();
        return redirect()->route('mapel.index')->with('success', 'Mata Pelajaran Dihapus!');
    }
}