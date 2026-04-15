<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KegiatanSeeder extends Seeder
{
    public function run(): void
    {
        $kegiatan = [
            ['nama' => 'Kegiatan 5S (Senyum, Salam, Sapa, Sopan, Santun)', 'desc' => 'Dilaksanakan setiap pagi jam ke-0'],
            ['nama' => 'Upacara Bendera', 'desc' => 'Khusus hari Senin'],
            ['nama' => 'Shalat Dhuhur Berjamaah', 'desc' => 'Dilaksanakan sebelum pulang'],
            ['nama' => 'Pra-KBM: Senam', 'desc' => 'Kegiatan pagi hari Selasa/Kamis'],
            ['nama' => 'Pra-KBM: Literasi-Numerasi', 'desc' => 'Kegiatan pagi hari Rabu'],
            ['nama' => 'Pra-KBM: Kerohanian', 'desc' => 'Kegiatan pagi di Masjid'],
            ['nama' => 'Kegiatan Kokurikuler / Jum\'at Berseri', 'desc' => 'Khusus hari Jumat pagi'],
            ['nama' => 'Tadarus Juz Amma', 'desc' => 'Khusus hari Sabtu pagi'],
        ];

        foreach ($kegiatan as $k) {
            DB::table('kegiatan')->updateOrInsert(
                ['nama_kegiatan' => $k['nama']],
                ['deskripsi' => $k['desc'], 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}