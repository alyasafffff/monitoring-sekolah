<?php

namespace App\Http\Controllers;

use App\Models\JamPelajaranConfig;
use Illuminate\Http\Request;

class JamPelajaranConfigController extends Controller
{
    public function index()
    {
        // Tetap urutkan berdasarkan grup hari dan nomor jam
        $configs = JamPelajaranConfig::orderBy('hari_grup')
                    ->orderBy('jam_ke')
                    ->get();
        return view('dashboard.jam_config.index', compact('configs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'hari_grup'   => 'required|string|max:50', // Sekarang string, lebih fleksibel
            'jam_ke'      => 'required|integer',
            'jam_mulai'   => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
            'tipe'        => 'required|in:mapel,istirahat,kegiatan',
            'keterangan'  => 'nullable|string|max:100',
        ]);

        // Cek duplikasi: tidak boleh ada jam_ke yang sama di grup yang sama
        $exists = JamPelajaranConfig::where('hari_grup', $request->hari_grup)
                    ->where('jam_ke', $request->jam_ke)
                    ->exists();

        if ($exists) {
            return back()->with('error', "Jam Ke-{$request->jam_ke} untuk grup '{$request->hari_grup}' sudah ada!");
        }

        // Simpan data (is_active default true jika tidak dikirim)
        JamPelajaranConfig::create([
            'hari_grup'   => $request->hari_grup,
            'jam_ke'      => $request->jam_ke,
            'jam_mulai'   => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'tipe'        => $request->tipe,
            'keterangan'  => $request->keterangan
        ]);

        return redirect()->back()->with('success', 'Konfigurasi waktu berhasil disimpan!');
    }

    // Tambahkan fungsi toggle status jika ingin admin bisa mematikan jam tanpa menghapus
    public function toggleStatus($id)
    {
        $config = JamPelajaranConfig::findOrFail($id);
        $config->is_active = !$config->is_active;
        $config->save();

        return redirect()->back()->with('success', 'Status jam berhasil diperbarui!');
    }

    public function destroy($id)
    {
        JamPelajaranConfig::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Konfigurasi jam berhasil dihapus!');
    }
}