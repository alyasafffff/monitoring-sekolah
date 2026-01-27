<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ==========================================
        // 1. DATA MASTER: USERS (GURU, STAFF, DLL)
        // ==========================================
        // Password default: 12345678
        $password = Hash::make('12345678');

        $users = [
            // Admin TU
            ['nip' => '11111', 'name' => 'Admin TU', 'password' => $password, 'role' => 'admin', 'created_at' => now()],
            // Guru 1 (Wali Kelas 7A)
            ['nip' => '22222', 'name' => 'Budi Santoso, S.Pd', 'password' => $password, 'role' => 'guru', 'created_at' => now()],
            // Guru 2 (Guru Mapel Biasa)
            ['nip' => '33333', 'name' => 'Siti Aminah, M.Pd', 'password' => $password, 'role' => 'guru', 'created_at' => now()],
            // Guru BK
            ['nip' => '44444', 'name' => 'Pak Tono (BK)', 'password' => $password, 'role' => 'bk', 'created_at' => now()],
            // Kepala Sekolah
            ['nip' => '55555', 'name' => 'Kepala Sekolah', 'password' => $password, 'role' => 'kepsek', 'created_at' => now()],
        ];
        
        // Gunakan insertOrIgnore atau upsert agar tidak error jika dijalankan ulang tanpa migrate:fresh
        DB::table('users')->upsert($users, ['nip'], ['name', 'role', 'password']);


        // ==========================================
        // 2. DATA MASTER: MATA PELAJARAN
        // ==========================================
        // Pastikan nama tabel di migration sesuai ('mata_pelajaran' atau 'mata_pelajarans')
        $mapelId1 = DB::table('mata_pelajaran')->insertGetId(['kode_mapel' => 'MTK01', 'nama_mapel' => 'Matematika', 'created_at' => now()]);
        $mapelId2 = DB::table('mata_pelajaran')->insertGetId(['kode_mapel' => 'IPA01', 'nama_mapel' => 'Ilmu Pengetahuan Alam', 'created_at' => now()]);


        // ==========================================
        // 3. DATA MASTER: KELAS
        // ==========================================
        // Ambil ID Guru Pak Budi untuk jadi Wali Kelas
        $guru1_id = DB::table('users')->where('nip', '22222')->value('id');
        
        $kelas7a_id = DB::table('kelas')->insertGetId(['nama_kelas' => '7A', 'wali_kelas_id' => $guru1_id, 'created_at' => now()]);
        $kelas7b_id = DB::table('kelas')->insertGetId(['nama_kelas' => '7B', 'wali_kelas_id' => null, 'created_at' => now()]);


        // ==========================================
        // 4. DATA MASTER: SISWA
        // ==========================================
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


        // ==========================================
        // 5. DATA JADWAL (UNTUK TESTING API JURNAL)
        // ==========================================
        // Otomatis membuat jadwal untuk HARI INI agar bisa langsung test input jurnal
        $hari_ini = Carbon::now()->isoFormat('dddd'); // Senin, Selasa, dst (Pastikan locale ID aktif)
        // Fallback manual jika Carbon locale bukan ID
        if (!in_array($hari_ini, ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'])) {
            $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            $hari_ini = $days[now()->dayOfWeek];
        }

        DB::table('jadwal_pelajaran')->insert([
            'kelas_id'    => $kelas7a_id,
            'mapel_id'    => $mapelId1,   // Matematika
            'guru_id'     => $guru1_id,   // Pak Budi
            'hari'        => $hari_ini,   // Hari ini
            'jam_mulai'   => '07:00:00',
            'jam_selesai' => '08:30:00',
            'created_at'  => now(),
        ]);
    }
}