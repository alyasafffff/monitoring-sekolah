<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // =========================
        // 1. USERS
        // =========================
        $password = Hash::make('12345678');

        $users = [
            ['nip' => '11111', 'name' => 'Admin Sistem', 'password' => $password, 'role' => 'admin'],
            ['nip' => '22222', 'name' => 'Budi Santoso, S.Pd', 'password' => $password, 'role' => 'guru'], // Walas 7A
            ['nip' => '33333', 'name' => 'Siti Aminah, M.Pd', 'password' => $password, 'role' => 'guru'],  // Walas 7B
            ['nip' => '66666', 'name' => 'Ahmad Fauzi, S.T', 'password' => $password, 'role' => 'guru'],   // Guru Mapel
            ['nip' => '77777', 'name' => 'Dewi Lestari, M.Si', 'password' => $password, 'role' => 'guru'], // Guru Mapel
            ['nip' => '44444', 'name' => 'Pak Tono (BK)', 'password' => $password, 'role' => 'bk'],
            ['nip' => '55555', 'name' => 'Kepala Sekolah', 'password' => $password, 'role' => 'kepsek'],
        ];
        DB::table('users')->upsert($users, ['nip'], ['name', 'role', 'password']);


        // =========================
        // 2. KELAS
        // =========================
        $guruBudiId = DB::table('users')->where('nip', '22222')->value('id');
        $guruSitiId = DB::table('users')->where('nip', '33333')->value('id'); // ID untuk Siti Aminah

        $tingkat = ['7', '8', '9'];
        $paralel = ['A', 'B', 'C', 'D', 'E'];
        $kelasData = [];

        foreach ($tingkat as $t) {
            foreach ($paralel as $p) {
                $namaKelas = $t . $p;

                // Tentukan Wali Kelas secara spesifik
                $walasId = null;
                if ($namaKelas === '7A') $walasId = $guruBudiId;
                if ($namaKelas === '7B') $walasId = $guruSitiId;

                $id = DB::table('kelas')->insertGetId([
                    'nama_kelas'    => $namaKelas,
                    'wali_kelas_id' => $walasId,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);

                $kelasData[] = ['id' => $id, 'nama' => $namaKelas];
            }
        }
        // =========================
        // 3. SISWA
        // =========================
        $namaDepanL = ['Aditya', 'Bagas', 'Catur', 'Dika', 'Eko', 'Fajar', 'Galang', 'Heri', 'Irfan', 'Joko'];
        $namaDepanP = ['Anisa', 'Bella', 'Citra', 'Dinda', 'Endah', 'Fitri', 'Gita', 'Hana', 'Indah', 'Jelita'];
        $namaBelakang = ['Saputra', 'Pratama', 'Wijaya', 'Kusuma', 'Sari', 'Utami', 'Putra', 'Putri'];

        foreach ($kelasData as $kelas) {

            // ✅ LOGIC JUMLAH SISWA
            $jumlahSiswa = in_array($kelas['nama'], ['7A', '7B']) ? 30 : 3;

            for ($i = 1; $i <= $jumlahSiswa; $i++) {
                $gender = ($i % 2 == 0) ? 'P' : 'L';

                $nama = ($gender == 'L')
                    ? $namaDepanL[array_rand($namaDepanL)] . ' ' . $namaBelakang[array_rand($namaBelakang)]
                    : $namaDepanP[array_rand($namaDepanP)] . ' ' . $namaBelakang[array_rand($namaBelakang)];

                DB::table('siswa')->insert([
                    'nisn' => rand(1000000000, 9999999999),
                    'nama_siswa' => $nama,
                    'jenis_kelamin' => $gender,
                    'tanggal_lahir' => now()->subYears(rand(13, 15)),
                    'alamat' => 'Jl. Contoh Alamat',
                    'no_hp_ortu' => '08' . rand(111111111, 999999999),
                    'kelas_id' => $kelas['id'],
                    'qr_token' => 'QR-' . $kelas['nama'] . '-' . Str::random(8),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // =========================
        // 4. MAPEL & JADWAL
        // =========================
        $this->call([
            MapelSeeder::class,    // Mengisi config jam & daftar mapel
            KegiatanSeeder::class, // Mengisi daftar kegiatan (BARU)
            JadwalSeeder::class,
        ]);
    }
}
