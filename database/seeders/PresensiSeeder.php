<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PresensiSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Membersihkan data percobaan sebelumnya...');
        DB::table('jurnals')->where('catatan', 'like', '%Digenerate otomatis%')->delete();

        // 1. Ambil ID Kelas KHUSUS untuk "7A" dan "7B"
        $kelasIds = DB::table('kelas')
            ->whereIn('nama_kelas', ['7A', '7B'])
            ->pluck('id');

        if ($kelasIds->isEmpty()) {
            $this->command->error('Gagal! Kelas 7A dan 7B tidak ditemukan.');
            return;
        }

        // 2. Rentang waktu (1 Mei 2026 s/d Hari ini)
        $startDate = Carbon::create(2026, 5, 1); 
        $endDate = Carbon::now();

        $hariMap = [
            0 => 'Minggu', 1 => 'Senin', 2 => 'Selasa', 
            3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu',
        ];

        $jadwals = DB::table('jadwal_pelajaran')->whereIn('kelas_id', $kelasIds)->get();
        $siswasByKelas = DB::table('siswa')->whereIn('kelas_id', $kelasIds)->get()->groupBy('kelas_id');

        $presensiToInsert = [];

        $this->command->info('Memulai generate data presensi konsisten harian...');

        // 3. Looping Tanggal
        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
            
            $dayName = $hariMap[$date->dayOfWeek];

            if ($dayName == 'Minggu') {
                continue; // Skip hari Minggu
            }

            $dailySchedules = $jadwals->where('hari', $dayName);

            if ($dailySchedules->isEmpty()) {
                continue;
            }

            // A. Buat Jurnal untuk setiap jam pelajaran di hari tersebut
            $jurnalMap = [];
            foreach ($dailySchedules as $jadwal) {
                $jurnalId = DB::table('jurnals')->insertGetId([
                    'jadwal_id' => $jadwal->id,
                    'guru_id' => $jadwal->guru_id,
                    'tanggal' => $date->format('Y-m-d'),
                    'materi' => 'Materi Simulasi KBM',
                    'catatan' => 'Digenerate otomatis oleh Seeder',
                    'status_guru' => 'Hadir',
                    'status_pengisian' => 'selesai',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $jurnalMap[$jadwal->kelas_id][] = $jurnalId;
            }

            // B. Tentukan Status Siswa PER HARI (Bukan per jam pelajaran)
            foreach ($kelasIds as $kId) {
                $siswas = $siswasByKelas->get($kId, collect());
                $jurnalsHariIni = $jurnalMap[$kId] ?? [];

                foreach ($siswas as $siswa) {
                    

                    $rand = rand(1, 100);
                    if ($rand <= 90) {
                        $statusHarian = 'Hadir';
                    } elseif ($rand <= 95) {
                        $statusHarian = 'Sakit';
                    } elseif ($rand <= 98) {
                        $statusHarian = 'Izin';
                    } else {
                        $statusHarian = 'Alpha'; 
                    }

                    
                    foreach ($jurnalsHariIni as $jId) {
                        $presensiToInsert[] = [
                            'jurnal_id' => $jId,
                            'siswa_id' => $siswa->id,
                            'status' => $statusHarian,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
            }
        }

        // 4. Insert data ke Database
        foreach (array_chunk($presensiToInsert, 1000) as $chunk) {
            DB::table('presensi_detail')->insert($chunk);
        }

        $this->command->info('Selesai! Data rekap presensi harian berhasil diperbaiki.');
    }
}