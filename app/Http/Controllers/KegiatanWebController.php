<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KegiatanWebController extends Controller
{
    public function index()
    {
        // Mengambil data dari tabel 'kegiatan' yang baru dibuat
        $kegiatan = DB::table('kegiatan')->orderBy('nama_kegiatan', 'asc')->get();
        return view('dashboard.kegiatan.index', compact('kegiatan'));
    }

    public function store(Request $request)
{
    // 1. Validasi Input
    $request->validate([
        'nama_kegiatan' => 'required|string|max:255',
        'deskripsi' => 'nullable|string',
    ]);

    // 2. Simpan ke Database
    DB::table('kegiatan')->insert([
        'nama_kegiatan' => $request->nama_kegiatan,
        'deskripsi' => $request->deskripsi,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // 3. REDIRECT ke halaman index dengan pesan sukses
    return redirect()->route('kegiatan.index')->with('success', 'Kegiatan sekolah berhasil ditambahkan!');
}

    public function destroy($id)
    {
        DB::table('kegiatan')->where('id', $id)->delete();
        return redirect()->back()->with('success', 'Kegiatan berhasil dihapus!');
    }

    public function create()
    {
        return view('dashboard.kegiatan.create');
    }
    public function edit($id)
    {
        $kegiatan = DB::table('kegiatan')->where('id', $id)->first();
        return view('dashboard.kegiatan.edit', compact('kegiatan'));
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        DB::table('kegiatan')->where('id', $id)->update([
            'nama_kegiatan' => $request->nama_kegiatan,
            'deskripsi' => $request->deskripsi,
            'updated_at' => now(),
        ]);

        return redirect()->route('kegiatan.index')->with('success', 'Kegiatan berhasil diperbarui!');
    }
}
