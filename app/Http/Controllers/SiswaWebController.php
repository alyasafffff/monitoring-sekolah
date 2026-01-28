<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SiswaWebController extends Controller
{
    // 1. TAMPILKAN DATA (READ)
    public function index()
    {
        $siswa = DB::table('siswa')
            ->join('kelas', 'siswa.kelas_id', '=', 'kelas.id')
            ->select('siswa.*', 'kelas.nama_kelas')
            ->orderBy('kelas.nama_kelas', 'asc')
            ->orderBy('siswa.nama_siswa', 'asc')
            ->get();

        return view('dashboard.siswa.index', compact('siswa'));
    }

    // 2. FORM TAMBAH (CREATE)
    public function create()
    {
        // Kita butuh data kelas untuk Dropdown Pilihan
        $kelas = DB::table('kelas')->orderBy('nama_kelas', 'asc')->get();
        return view('dashboard.siswa.create', compact('kelas'));
    }

    // 3. PROSES SIMPAN (STORE)
    public function store(Request $request)
    {
        $request->validate([
            'nisn' => 'required|unique:siswa,nisn',
            'nama_siswa' => 'required|string',
            'kelas_id' => 'required|exists:kelas,id',
            'jenis_kelamin' => 'required|in:L,P',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',
            'no_hp_ortu' => 'nullable|string|max:15'
        ]);

        // Generate QR Token otomatis
        $nama_depan = explode(' ', trim($request->nama_siswa))[0];
        $token_otomatis = 'SMPN2-' . $nama_depan . '-' . Str::random(6);

        DB::table('siswa')->insert([
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

        return redirect()->route('siswa.index')->with('success', 'Data Siswa Berhasil Ditambahkan!');
    }

    // 4. FORM EDIT (EDIT)
    public function edit($id)
    {
        $siswa = DB::table('siswa')->where('id', $id)->first();
        $kelas = DB::table('kelas')->orderBy('nama_kelas', 'asc')->get();

        return view('dashboard.siswa.edit', compact('siswa', 'kelas'));
    }

    // 5. PROSES UPDATE (UPDATE)
    public function update(Request $request, $id)
    {
        $request->validate([
            'nisn' => 'required|unique:siswa,nisn,' . $id,
            'nama_siswa' => 'required|string',
            'kelas_id' => 'required|exists:kelas,id',
            'jenis_kelamin' => 'required|in:L,P',
        ]);

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

        return redirect()->route('siswa.index')->with('success', 'Data Siswa Berhasil Diperbarui!');
    }

    // 6. HAPUS DATA (DESTROY)
    public function destroy($id)
    {
        DB::table('siswa')->where('id', $id)->delete();
        return redirect()->route('siswa.index')->with('success', 'Data Siswa Berhasil Dihapus!');
    }

    public function cetakKartu($id)
    {
        $siswa = DB::table('siswa')
            ->join('kelas', 'siswa.kelas_id', '=', 'kelas.id')
            ->select('siswa.*', 'kelas.nama_kelas')
            ->where('siswa.id', $id)
            ->first();

        return view('dashboard.siswa.cetak_kartu', compact('siswa'));
    }
}