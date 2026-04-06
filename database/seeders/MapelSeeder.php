<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MapelSeeder extends Seeder
{
    public function run(): void
    {
        // =========================
        // 1. JAM PELAJARAN CONFIG
        // =========================
        $jamConfigs = [

            // SENIN
            ['hari_grup' => 'Senin', 'jam_ke' => 0, 'jam_mulai' => '06:45', 'jam_selesai' => '07:00', 'tipe' => 'kegiatan', 'keterangan' => 'Kegiatan 5S'],
            ['hari_grup' => 'Senin', 'jam_ke' => 1, 'jam_mulai' => '07:00', 'jam_selesai' => '07:55', 'tipe' => 'kegiatan', 'keterangan' => 'Upacara Bendera'],
            ['hari_grup' => 'Senin', 'jam_ke' => 2, 'jam_mulai' => '07:55', 'jam_selesai' => '08:30', 'tipe' => 'mapel', 'keterangan' => 'Jam Ke-2'],
            ['hari_grup' => 'Senin', 'jam_ke' => 3, 'jam_mulai' => '08:30', 'jam_selesai' => '09:05', 'tipe' => 'mapel', 'keterangan' => 'Jam Ke-3'],
            ['hari_grup' => 'Senin', 'jam_ke' => 4, 'jam_mulai' => '09:05', 'jam_selesai' => '09:40', 'tipe' => 'mapel', 'keterangan' => 'Jam Ke-4'],
            ['hari_grup' => 'Senin', 'jam_ke' => 5, 'jam_mulai' => '09:40', 'jam_selesai' => '10:00', 'tipe' => 'istirahat', 'keterangan' => 'Istirahat'],
            ['hari_grup' => 'Senin', 'jam_ke' => 6, 'jam_mulai' => '10:00', 'jam_selesai' => '10:35', 'tipe' => 'mapel', 'keterangan' => 'Jam Ke-5'],
            ['hari_grup' => 'Senin', 'jam_ke' => 7, 'jam_mulai' => '10:35', 'jam_selesai' => '11:10', 'tipe' => 'mapel', 'keterangan' => 'Jam Ke-6'],
            ['hari_grup' => 'Senin', 'jam_ke' => 8, 'jam_mulai' => '11:10', 'jam_selesai' => '11:45', 'tipe' => 'mapel', 'keterangan' => 'Jam Ke-7'],
            ['hari_grup' => 'Senin', 'jam_ke' => 9, 'jam_mulai' => '11:45', 'jam_selesai' => '12:20', 'tipe' => 'mapel', 'keterangan' => 'Jam Ke-8'],
            ['hari_grup' => 'Senin', 'jam_ke' => 10, 'jam_mulai' => '12:20', 'jam_selesai' => '12:50', 'tipe' => 'kegiatan', 'keterangan' => 'Shalat Dhuhur Berjamaah'],

            // --- POLA REGULER (Selasa, Rabu, Kamis, Sabtu) ---
            ['hari_grup' => 'Reguler', 'jam_ke' => 0, 'jam_mulai' => '06:45', 'jam_selesai' => '07:00', 'tipe' => 'kegiatan', 'keterangan' => 'Kegiatan 5S'],
            ['hari_grup' => 'Reguler', 'jam_ke' => 1, 'jam_mulai' => '07:00', 'jam_selesai' => '07:20', 'tipe' => 'kegiatan', 'keterangan' => 'Pra-KBM (Senam/Literasi/Kerohanian)'],
            ['hari_grup' => 'Reguler', 'jam_ke' => 2, 'jam_mulai' => '07:20', 'jam_selesai' => '07:55', 'tipe' => 'mapel', 'keterangan' => 'Jam Ke-1'],
            ['hari_grup' => 'Reguler', 'jam_ke' => 3, 'jam_mulai' => '07:55', 'jam_selesai' => '08:30', 'tipe' => 'mapel', 'keterangan' => 'Jam Ke-2'],
            ['hari_grup' => 'Reguler', 'jam_ke' => 4, 'jam_mulai' => '08:30', 'jam_selesai' => '09:05', 'tipe' => 'mapel', 'keterangan' => 'Jam Ke-3'],
            ['hari_grup' => 'Reguler', 'jam_ke' => 5, 'jam_mulai' => '09:05', 'jam_selesai' => '09:40', 'tipe' => 'mapel', 'keterangan' => 'Jam Ke-4'],
            ['hari_grup' => 'Reguler', 'jam_ke' => 6, 'jam_mulai' => '09:40', 'jam_selesai' => '10:00', 'tipe' => 'istirahat', 'keterangan' => 'Istirahat'],
            ['hari_grup' => 'Reguler', 'jam_ke' => 7, 'jam_mulai' => '10:00', 'jam_selesai' => '10:35', 'tipe' => 'mapel', 'keterangan' => 'Jam Ke-5'],
            ['hari_grup' => 'Reguler', 'jam_ke' => 8, 'jam_mulai' => '10:35', 'jam_selesai' => '11:10', 'tipe' => 'mapel', 'keterangan' => 'Jam Ke-6'],
            ['hari_grup' => 'Reguler', 'jam_ke' => 9, 'jam_mulai' => '11:10', 'jam_selesai' => '11:45', 'tipe' => 'mapel', 'keterangan' => 'Jam Ke-7'],
            ['hari_grup' => 'Reguler', 'jam_ke' => 10, 'jam_mulai' => '11:45', 'jam_selesai' => '12:20', 'tipe' => 'mapel', 'keterangan' => 'Jam Ke-8'],
            ['hari_grup' => 'Reguler', 'jam_ke' => 11, 'jam_mulai' => '12:20', 'jam_selesai' => '12:50', 'tipe' => 'kegiatan', 'keterangan' => 'Shalat Dhuhur Berjamaah'],

            // --- POLA HARI JUMAT (Jumat Berseri) ---
            ['hari_grup' => 'Jumat', 'jam_ke' => 0, 'jam_mulai' => '06:45', 'jam_selesai' => '07:00', 'tipe' => 'kegiatan', 'keterangan' => 'Kegiatan 5S'],
            ['hari_grup' => 'Jumat', 'jam_ke' => 1, 'jam_mulai' => '07:00', 'jam_selesai' => '07:40', 'tipe' => 'kegiatan', 'keterangan' => 'Kegiatan Kokurikuler 1'],
            ['hari_grup' => 'Jumat', 'jam_ke' => 2, 'jam_mulai' => '07:40', 'jam_selesai' => '08:20', 'tipe' => 'kegiatan', 'keterangan' => 'Kegiatan Kokurikuler 2'],
            ['hari_grup' => 'Jumat', 'jam_ke' => 3, 'jam_mulai' => '08:20', 'jam_selesai' => '09:00', 'tipe' => 'kegiatan', 'keterangan' => 'Kegiatan Kokurikuler 3'],
            ['hari_grup' => 'Jumat', 'jam_ke' => 4, 'jam_mulai' => '09:00', 'jam_selesai' => '09:20', 'tipe' => 'istirahat', 'keterangan' => 'Istirahat'],
            ['hari_grup' => 'Jumat', 'jam_ke' => 5, 'jam_mulai' => '09:20', 'jam_selesai' => '09:55', 'tipe' => 'mapel', 'keterangan' => 'Jam Ke-1'],
            ['hari_grup' => 'Jumat', 'jam_ke' => 6, 'jam_mulai' => '09:55', 'jam_selesai' => '10:30', 'tipe' => 'mapel', 'keterangan' => 'Jam Ke-2'],
        ];

        foreach ($jamConfigs as $jc) {
            DB::table('jam_pelajaran_config')->updateOrInsert(
                ['hari_grup' => $jc['hari_grup'], 'jam_ke' => $jc['jam_ke']],
                array_merge($jc, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        // =========================
        // 2. MATA PELAJARAN
        // =========================
        $mapels = [
            ['kode' => 'PAI01', 'nama' => 'Pendidikan Agama'],
            ['kode' => 'PPK01', 'nama' => 'Pendidikan Pancasila'],
            ['kode' => 'BIN01', 'nama' => 'Bahasa Indonesia'],
            ['kode' => 'MTK01', 'nama' => 'Matematika'],
            ['kode' => 'BIG01', 'nama' => 'Bahasa Inggris'],
            ['kode' => 'IPA01', 'nama' => 'IPA'],
            ['kode' => 'IPS01', 'nama' => 'IPS'],
            ['kode' => 'INF01', 'nama' => 'Informatika'],
            ['kode' => 'PJOK01', 'nama' => 'PJOK'],
            ['kode' => 'SBD01', 'nama' => 'Seni Budaya'],
        ];

        foreach ($mapels as $m) {
            DB::table('mata_pelajaran')->updateOrInsert(
                ['kode_mapel' => $m['kode']],
                [
                    'nama_mapel' => $m['nama'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}