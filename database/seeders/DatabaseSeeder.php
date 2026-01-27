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
        // 1. Buat Data Users (Semua passwordnya: 12345678)
        $users = [
            // Admin TU
            ['nip' => '11111', 'name' => 'Admin TU', 'password' => Hash::make('12345678'), 'role' => 'admin', 'created_at' => now()],
            // Guru 1 (Akan jadi Wali Kelas 7A)
            ['nip' => '22222', 'name' => 'Budi Santoso, S.Pd', 'password' => Hash::make('12345678'), 'role' => 'guru', 'created_at' => now()],
            // Guru 2 (Guru Mapel Biasa)
            ['nip' => '33333', 'name' => 'Siti Aminah, M.Pd', 'password' => Hash::make('12345678'), 'role' => 'guru', 'created_at' => now()],
            // Guru BK
            ['nip' => '44444', 'name' => 'Pak Tono (BK)', 'password' => Hash::make('12345678'), 'role' => 'bk', 'created_at' => now()],
            // Kepala Sekolah
            ['nip' => '55555', 'name' => 'Kepala Sekolah', 'password' => Hash::make('12345678'), 'role' => 'kepsek', 'created_at' => now()],
        ];
        DB::table('users')->insert($users);

        // 2. Buat Data Mata Pelajaran
        $mapelId1 = DB::table('mata_pelajaran')->insertGetId(['kode_mapel' => 'MTK01', 'nama_mapel' => 'Matematika', 'created_at' => now()]);
        $mapelId2 = DB::table('mata_pelajaran')->insertGetId(['kode_mapel' => 'IPA01', 'nama_mapel' => 'Ilmu Pengetahuan Alam', 'created_at' => now()]);

        // 3. Buat Data Kelas (Pak Budi NIP 22222 jadi Wali Kelas 7A)
        $guru1_id = DB::table('users')->where('nip', '22222')->first()->id;
        $kelas7a_id = DB::table('kelas')->insertGetId(['nama_kelas' => '7A', 'wali_kelas_id' => $guru1_id, 'created_at' => now()]);
        $kelas7b_id = DB::table('kelas')->insertGetId(['nama_kelas' => '7B', 'wali_kelas_id' => null, 'created_at' => now()]);

        // 4. Buat Data Siswa Kelas 7A (DENGAN IDENTITAS LENGKAP)
        $siswa = [
            [
                'nisn' => '001', 
                'nama_siswa' => 'Ahmad Fikri', 
                'kelas_id' => $kelas7a_id, 
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => '2011-03-10',
                'alamat' => 'Jl. Pahlawan No. 1, Prigen',
                'no_hp_ortu' => '081234567890',
                'qr_token' => Str::random(40), 
                'created_at' => now()
            ],
            [
                'nisn' => '002', 
                'nama_siswa' => 'Bunga Citra', 
                'kelas_id' => $kelas7a_id, 
                'jenis_kelamin' => 'P',
                'tanggal_lahir' => '2011-07-22',
                'alamat' => 'Jl. Raya Trawas No. 45, Prigen',
                'no_hp_ortu' => '085712345678',
                'qr_token' => Str::random(40), 
                'created_at' => now()
            ],
            [
                'nisn' => '003', 
                'nama_siswa' => 'Candra Wijaya', 
                'kelas_id' => $kelas7a_id, 
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => '2011-11-05',
                'alamat' => 'Perumahan Indah Asri Blok B, Prigen',
                'no_hp_ortu' => '087898765432',
                'qr_token' => Str::random(40), 
                'created_at' => now()
            ],
        ];
        DB::table('siswa')->insert($siswa);

        // 5. Buat Data Jadwal Pelajaran (Hari ini)
        // Kita asumsikan hari ini Senin, Pak Budi ngajar MTK di 7A jam 07:00 - 08:30
        $hari_ini = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'][now()->dayOfWeek - 1] ?? 'Senin';
        DB::table('jadwal_pelajaran')->insert([
            'kelas_id' => $kelas7a_id,
            'mapel_id' => $mapelId1,
            'guru_id' => $guru1_id,
            'hari' => $hari_ini,
            'jam_mulai' => '07:00:00',
            'jam_selesai' => '08:30:00',
            'created_at' => now(),
        ]);
    }
}