<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Jurnal;
use Illuminate\Http\Request;

class JurnalController extends Controller
{
    // 1. GET ALL
    public function index()
    {
        $jurnal = Jurnal::with([
            'jadwal.guru:id,name,nip',
            'jadwal.kelas:id,nama_kelas',
            'jadwal.mapel:id,nama_mapel'
        ])->orderBy('tanggal', 'desc')->get();

        return response()->json(['success' => true, 'data' => $jurnal]);
    }

    // 2. CREATE (Pakai updateOrCreate biar tidak duplikat di hari yang sama)
    public function store(Request $request)
    {
        $request->validate([
            'jadwal_id' => 'required|exists:jadwal_pelajaran,id',
            'tanggal'   => 'required|date',
            'materi'    => 'required|string',
            'status'    => 'in:Hadir,Izin,Sakit'
        ]);

        // Logic: Jika Jurnal untuk jadwal ini di tanggal ini sudah ada, Update. Jika belum, Create.
        $jurnal = Jurnal::updateOrCreate(
            [
                'jadwal_id' => $request->jadwal_id,
                'tanggal'   => $request->tanggal
            ],
            [
                'materi' => $request->materi,
                'status' => $request->status ?? 'Hadir'
            ]
        );

        return response()->json(['success' => true, 'message' => 'Jurnal berhasil disimpan!', 'data' => $jurnal], 201);
    }

    // 3. SHOW
    public function show($id)
    {
        $jurnal = Jurnal::with(['jadwal.guru', 'jadwal.kelas', 'jadwal.mapel'])->find($id);
        if (!$jurnal) return response()->json(['success' => false, 'message' => 'Jurnal tidak ditemukan'], 404);

        return response()->json(['success' => true, 'data' => $jurnal]);
    }

    // 4. DELETE
    public function destroy($id)
    {
        $jurnal = Jurnal::find($id);
        if (!$jurnal) return response()->json(['success' => false, 'message' => 'Jurnal tidak ditemukan'], 404);

        $jurnal->delete();
        return response()->json(['success' => true, 'message' => 'Jurnal berhasil dihapus!']);
    }
}