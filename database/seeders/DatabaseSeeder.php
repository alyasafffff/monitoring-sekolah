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
        // 1. DATA MASTER: USERS (Tetap)
        // ==========================================
        $password = Hash::make('12345678');
        $users = [
            ['nip' => '11111', 'name' => 'Admin Sistem', 'password' => $password, 'role' => 'admin', 'created_at' => now()],
            ['nip' => '22222', 'name' => 'Budi Santoso, S.Pd', 'password' => $password, 'role' => 'guru', 'created_at' => now()],
            ['nip' => '33333', 'name' => 'Siti Aminah, M.Pd', 'password' => $password, 'role' => 'guru', 'created_at' => now()],
            ['nip' => '44444', 'name' => 'Pak Tono (BK)', 'password' => $password, 'role' => 'bk', 'created_at' => now()],
            ['nip' => '55555', 'name' => 'Kepala Sekolah', 'password' => $password, 'role' => 'kepsek', 'created_at' => now()],
        ];
        DB::table('users')->upsert($users, ['nip'], ['name', 'role', 'password']);

        $jamConfigs = [
            // --- POLA HARI SENIN (Ada Upacara) ---
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
                array_merge($jc, ['is_active' => true, 'created_at' => now(), 'updated_at' => now()])
            );
        }

        // ==========================================
        // 3. DATA MASTER: MATA PELAJARAN (Tetap)
        // ==========================================
        $mapels = [
            ['kode' => 'PAI01', 'nama' => 'Pendidikan Agama & Budi Pekerti'],
            ['kode' => 'PPK01', 'nama' => 'Pendidikan Pancasila'],
            ['kode' => 'BIN01', 'nama' => 'Bahasa Indonesia'],
            ['kode' => 'MTK01', 'nama' => 'Matematika Umum'],
            ['kode' => 'MTK01', 'nama' => 'Matematika Umum'],
            ['kode' => 'SEJ01', 'nama' => 'Sejarah Indonesia'],
            ['kode' => 'BIG01', 'nama' => 'Bahasa Inggris'],
            ['kode' => 'SBD01', 'nama' => 'Seni Budaya'],
            ['kode' => 'PJO01', 'nama' => 'Penjasorkes'],
            ['kode' => 'FIS01', 'nama' => 'Fisika'],
            ['kode' => 'BIO01', 'nama' => 'Biologi'],
            ['kode' => 'KIM01', 'nama' => 'Kimia'],
            ['kode' => 'EKO01', 'nama' => 'Ekonomi'],
            ['kode' => 'GEO01', 'nama' => 'Geografi'],
            ['kode' => 'SOS01', 'nama' => 'Sosiologi'],
            ['kode' => 'INF01', 'nama' => 'Informatika'],
            ['kode' => 'WEB01', 'nama' => 'Pemrograman Web'],
            ['kode' => 'MOB01', 'nama' => 'Pemrograman Mobile'],
            ['kode' => 'DB01', 'nama' => 'Basis Data'],
            ['kode' => 'UIX01', 'nama' => 'Desain Grafis & UI/UX'],
            ['kode' => 'BJW01', 'nama' => 'Bahasa Jawa'],
        ];

        $mapelIds = [];
        foreach ($mapels as $m) {
            // Kita gunakan updateOrInsert supaya tidak error Duplicate Entry
            DB::table('mata_pelajaran')->updateOrInsert(
                ['kode_mapel' => $m['kode']], // Cari berdasarkan kode
                [
                    'nama_mapel' => $m['nama'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );

            // Ambil ID-nya untuk digunakan di bagian Jadwal (Poin 5)
            $mapelIds[] = DB::table('mata_pelajaran')->where('kode_mapel', $m['kode'])->value('id');
        }

        // 1. AMBIL GURU UNTUK WALI KELAS 7A (Pastikan sudah ada user dengan role guru)
        // Jika belum ada, pastikan seeder User dijalankan duluan
        $guruBudiId = DB::table('users')->where('role', 'guru')->first()->id ?? null;

        // 2. DATA MASTER KELAS
        $tingkat = ['7', '8', '9'];
        $paralel = ['A', 'B', 'C', 'D', 'E'];
        $kelasData = [];

        foreach ($tingkat as $t) {
            foreach ($paralel as $p) {
                $namaKelas = $t . $p;
                
                // Set Wali Kelas: Hanya untuk 7A, sisanya null
                $walasId = ($namaKelas === '7A') ? $guruBudiId : null;

                $id = DB::table('kelas')->insertGetId([
                    'nama_kelas' => $namaKelas,
                    'wali_kelas_id' => $walasId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $kelasData[] = ['id' => $id, 'nama' => $namaKelas];
            }
        }

        // 3. DATA SISWA REALISTIS
        $namaDepanL = ['Aditya', 'Bagas', 'Catur', 'Dika', 'Eko', 'Fajar', 'Galang', 'Heri', 'Irfan', 'Joko', 'Kevin', 'Lukman', 'Mahendra', 'Nanda', 'Oki'];
        $namaDepanP = ['Anisa', 'Bella', 'Citra', 'Dinda', 'Endah', 'Fitri', 'Gita', 'Hana', 'Indah', 'Jelita', 'Kartika', 'Lestari', 'Maya', 'Novi', 'Olive'];
        $namaBelakang = ['Saputra', 'Pratama', 'Wijaya', 'Kusuma', 'Sari', 'Lestari', 'Utami', 'Putra', 'Putri', 'Hidayat'];

        foreach ($kelasData as $kelas) {
            // 7A & 8A diisi 30 orang (buat tes scroll), lainnya 3 orang
            $jumlahSiswa = ($kelas['nama'] == '7A' || $kelas['nama'] == '8A') ? 30 : 3;

            for ($i = 1; $i <= $jumlahSiswa; $i++) {
                $gender = ($i % 2 == 0) ? 'P' : 'L';
                $namaSiswa = ($gender == 'L') 
                    ? $namaDepanL[array_rand($namaDepanL)] . ' ' . $namaBelakang[array_rand($namaBelakang)]
                    : $namaDepanP[array_rand($namaDepanP)] . ' ' . $namaBelakang[array_rand($namaBelakang)];

                DB::table('siswa')->insert([
                    'nisn' => rand(1000000000, 9999999999),
                    'nama_siswa' => $namaSiswa,
                    'jenis_kelamin' => $gender,
                    'tanggal_lahir' => Carbon::now()->subYears(rand(13, 15))->subDays(rand(1, 365))->format('Y-m-d'),
                    'alamat' => 'Jl. Raya Prigen, Pasuruan',
                    'no_hp_ortu' => '08' . rand(111111111, 999999999),
                    'kelas_id' => $kelas['id'],
                    'qr_token' => 'QR-' . $kelas['nama'] . '-' . Str::random(10),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // ==========================================
        // 5. DATA JADWAL (Tambahkan kolom 'hari')
        // ==========================================

        // Ambil ID config untuk Senin Jam Ke-2
        $configId = DB::table('jam_pelajaran_config')
            ->where('hari_grup', 'Senin')
            ->where('jam_ke', 2)
            ->value('id');

        if ($configId) {
            DB::table('jadwal_pelajaran')->insert([
                'kelas_id' => $kelasData[0]['id'], // Kelas 7A
                'mapel_id' => $mapelIds[3],        // Matematika
                'guru_id'  => $guruBudiId,
                'jam_pelajaran_config_id' => $configId,
                'hari'     => 'Senin', // <--- TAMBAHKAN BARIS INI
                'created_at' => now(),
            ]);
        }
    }
}
