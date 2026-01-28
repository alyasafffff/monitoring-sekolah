<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KelasWebController extends Controller
{
    // 1. LIHAT DATA KELAS
    public function index()
    {
        // Ambil data kelas beserta nama Wali Kelas-nya (Join ke tabel users)
        $kelas = DB::table('kelas')
            ->leftJoin('users', 'kelas.wali_kelas_id', '=', 'users.id') // Pakai Left Join biar kelas yg belum punya wali tetap muncul
            ->select('kelas.*', 'users.name as nama_wali_kelas')
            ->orderBy('kelas.nama_kelas', 'asc')
            ->get();

        return view('dashboard.kelas.index', compact('kelas'));
    }

    // 2. FORM TAMBAH
    public function create()
    {
        // Ambil daftar user yang Role-nya GURU untuk dropdown
        $guru = DB::table('users')->where('role', 'guru')->orderBy('name', 'asc')->get();
        
        return view('dashboard.kelas.create', compact('guru'));
    }

    // 3. SIMPAN DATA
    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required|unique:kelas,nama_kelas',
            'wali_kelas_id' => 'required|exists:users,id' // Wali kelas wajib dipilih
        ]);

        // Cek: Satu guru cuma boleh jadi wali kelas di 1 kelas aja
        $cekGuru = DB::table('kelas')->where('wali_kelas_id', $request->wali_kelas_id)->exists();
        if ($cekGuru) {
            return back()->withErrors(['wali_kelas_id' => 'Guru ini sudah menjadi Wali Kelas di kelas lain!']);
        }

        DB::table('kelas')->insert([
            'nama_kelas' => $request->nama_kelas,
            'wali_kelas_id' => $request->wali_kelas_id,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->route('kelas.index')->with('success', 'Kelas Berhasil Ditambahkan!');
    }

    // 4. FORM EDIT
    public function edit($id)
    {
        $kelas = DB::table('kelas')->where('id', $id)->first();
        $guru = DB::table('users')->where('role', 'guru')->orderBy('name', 'asc')->get();

        return view('dashboard.kelas.edit', compact('kelas', 'guru'));
    }

    // 5. UPDATE DATA
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kelas' => 'required|unique:kelas,nama_kelas,' . $id,
            'wali_kelas_id' => 'required|exists:users,id'
        ]);

        // Cek lagi guru ganda (kecuali punya dia sendiri)
        $cekGuru = DB::table('kelas')
                    ->where('wali_kelas_id', $request->wali_kelas_id)
                    ->where('id', '!=', $id) // Abaikan diri sendiri
                    ->exists();

        if ($cekGuru) {
            return back()->withErrors(['wali_kelas_id' => 'Guru ini sudah menjadi Wali Kelas di kelas lain!']);
        }

        DB::table('kelas')->where('id', $id)->update([
            'nama_kelas' => $request->nama_kelas,
            'wali_kelas_id' => $request->wali_kelas_id,
            'updated_at' => now()
        ]);

        return redirect()->route('kelas.index')->with('success', 'Data Kelas Berhasil Diperbarui!');
    }

    // 6. HAPUS DATA
    public function destroy($id)
    {
        // Cek dulu, kalau kelas masih ada siswanya, jangan dihapus!
        $adaSiswa = DB::table('siswa')->where('kelas_id', $id)->exists();
        
        if($adaSiswa) {
            return back()->with('error', 'Gagal! Kelas ini masih memiliki siswa. Pindahkan dulu siswanya.');
        }

        DB::table('kelas')->where('id', $id)->delete();
        return redirect()->route('kelas.index')->with('success', 'Kelas Berhasil Dihapus!');
    }
}