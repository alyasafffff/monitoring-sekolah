<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JadwalSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ambil ID Kelas target
        $targetKelas = DB::table('kelas')
            ->whereIn('nama_kelas', ['7A', '7B'])
            ->pluck('id', 'nama_kelas')
            ->toArray();

        // 2. Ambil Semua ID Guru dan Mapel
        $guruIds = DB::table('users')->where('role', 'guru')->pluck('id')->toArray();
        $mapels = DB::table('mata_pelajaran')->pluck('id')->toArray();

        if (empty($targetKelas) || empty($guruIds) || empty($mapels)) {
            echo "Data Master (Kelas/Guru/Mapel) belum lengkap!\n";
            return;
        }

        $hariList = [
            'Senin' => 'Senin',
            'Selasa' => 'Reguler',
            'Rabu' => 'Reguler',
            'Kamis' => 'Reguler',
            'Jumat' => 'Jumat',
        ];

        // Looping setiap kelas
        foreach ($targetKelas as $namaKelas => $kelasId) {
            foreach ($hariList as $hari => $grup) {
                // Ambil konfigurasi jam pelajaran tipe 'mapel'
                $jamConfigs = DB::table('jam_pelajaran_config')
                    ->where('hari_grup', $grup)
                    ->where('tipe', 'mapel')
                    ->orderBy('jam_ke')
                    ->get();

                foreach ($jamConfigs as $index => $jam) {
                    // Logika distribusi guru dan mapel agar variatif tiap jam dan kelas
                    $mapelId = $mapels[($index + $kelasId) % count($mapels)];
                    $guruId = $guruIds[($index + $kelasId) % count($guruIds)];

                    DB::table('jadwal_pelajaran')->updateOrInsert(
                        [
                            'kelas_id' => $kelasId,
                            'hari' => $hari,
                            'jam_pelajaran_config_id' => $jam->id,
                        ],
                        [
                            'mapel_id' => $mapelId,
                            'guru_id' => $guruId,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                }
            }
        }
        echo "Jadwal Full 1 Minggu untuk 7A dan 7B berhasil dibuat.\n";
    }
}