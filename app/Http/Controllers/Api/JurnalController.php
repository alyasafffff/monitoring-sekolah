<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Jurnal;
use Illuminate\Http\Request;

class JurnalController extends Controller
{
    // 1. GET ALL (Lihat Riwayat Jurnal)
    // 1. GET ALL (Lihat Riwayat Jurnal Lengkap)
    public function index()
    {
        $jurnal = Jurnal::with([
            // Ambil data jadwal, dan sekalian ambil data teman-temannya
            'jadwal.guru:id,name,nip',       // Ambil ID, Nama, NIP Guru saja
            'jadwal.kelas:id,nama_kelas',    // Ambil ID, Nama Kelas saja
            'jadwal.mapel:id,nama_mapel'     // Ambil ID, Nama Mapel saja
        ])
        ->orderBy('tanggal', 'desc')
        ->get();

        return response()->json([
            'success' => true,
            'data' => $jurnal
        ], 200);
    }

    // 2. CREATE (Simpan Jurnal Teks)
    // 2. CREATE (Simpan Jurnal)
    public function store(Request $request)
    {
        // Validasi
        $request->validate([
            'jadwal_id' => 'required|exists:jadwal_pelajaran,id',
            'tanggal'   => 'required|date',
            'materi'    => 'required|string',
            'status'    => 'in:Hadir,Izin,Sakit'
        ]);

        // Simpan ke Database
        $jurnal = Jurnal::create([
            'jadwal_id' => $request->jadwal_id,
            'tanggal'   => $request->tanggal,
            'materi'    => $request->materi,
            'status'    => $request->status ?? 'Hadir'
        ]);

        // <--- TAMBAHAN PENTING: Kita suruh Laravel ambil data relasinya sekarang juga
        $jurnal->load([
            'jadwal.guru:id,name,nip',
            'jadwal.kelas:id,nama_kelas',
            'jadwal.mapel:id,nama_mapel'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Jurnal berhasil disimpan!',
            'data' => $jurnal
        ], 201);
    }
}