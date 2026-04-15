<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JadwalSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ambil ID 7A dan 7B saja
        $targetKelas = DB::table('kelas')->whereIn('nama_kelas', ['7A', '7B'])->orderBy('nama_kelas')->pluck('id', 'nama_kelas')->toArray();
        $guruIds = DB::table('users')->where('role', 'guru')->pluck('id')->toArray();
        $mapels = DB::table('mata_pelajaran')->pluck('id', 'nama_mapel')->toArray();
        $kegId = DB::table('kegiatan')->pluck('id', 'nama_kegiatan')->toArray();

        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        foreach ($hariList as $hari) {
            $grup = ($hari == 'Senin') ? 'Senin' : (($hari == 'Jumat') ? 'Jumat' : 'Reguler');
            $configs = DB::table('jam_pelajaran_config')->where('hari_grup', $grup)->orderBy('jam_ke')->get();

            // Variabel untuk menyimpan pilihan guru/mapel agar bisa dipakai di jam berikutnya (Blok Waktu)
            $blokMapel = []; // ['7A' => id_mapel, '7B' => id_mapel]
            $blokGuru = [];  // ['7A' => id_guru, '7B' => id_guru]

            foreach ($configs as $idx => $jam) {
                
                // Siapkan daftar guru tersedia untuk jam ini (untuk 7A & 7B)
                $poolGuru = $guruIds;
                shuffle($poolGuru);

                foreach ($targetKelas as $namaKelas => $kelasId) {
                    $data = [
                        'kelas_id' => $kelasId,
                        'hari'     => $hari,
                        'jam_pelajaran_config_id' => $jam->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    if ($jam->tipe == 'mapel') {
                        // LOGIKA BLOK WAKTU (Misal Jam 2 & 3 Mapelnya SAMA)
                        // Kita cek apakah jam sebelumnya juga mapel? Jika iya, gunakan data yang sama
                        if ($idx > 0 && $configs[$idx-1]->tipe == 'mapel' && ($idx % 2 != 0)) {
                            $data['mapel_id'] = $blokMapel[$namaKelas];
                            $data['guru_id']  = $blokGuru[$namaKelas];
                        } else {
                            // Ambil Mapel & Guru Baru
                            $mapelTerpilih = array_values($mapels)[($idx + $kelasId) % count($mapels)];
                            
                            // Pastikan guru 7A != guru 7B
                            $guruTerpilih = ($namaKelas == '7A') ? $poolGuru[0] : $poolGuru[1];

                            $data['mapel_id'] = $mapelTerpilih;
                            $data['guru_id']  = $guruTerpilih;

                            // Simpan ke memori blok
                            $blokMapel[$namaKelas] = $mapelTerpilih;
                            $blokGuru[$namaKelas]  = $guruTerpilih;
                        }
                        $data['kegiatan_id'] = null;

                    } elseif ($jam->tipe == 'kegiatan') {
                        $walas = DB::table('kelas')->where('id', $kelasId)->first();
                        $namaKeg = $this->getNamaKegiatan($hari, $jam->keterangan);
                        
                        $data['kegiatan_id'] = $kegId[$namaKeg] ?? null;
                        $data['guru_id']     = $walas->wali_kelas_id ?? $guruIds[0];
                        $data['mapel_id']    = null;
                    } else {
                        // Istirahat, reset blok
                        $blokMapel[$namaKelas] = null;
                        $blokGuru[$namaKelas] = null;
                        continue;
                    }

                    DB::table('jadwal_pelajaran')->updateOrInsert(
                        ['kelas_id' => $kelasId, 'hari' => $hari, 'jam_pelajaran_config_id' => $jam->id],
                        $data
                    );
                }
            }
        }
        echo "Jadwal 7A & 7B (Blok 2 Jam) Berhasil Dibuat!\n";
    }

    private function getNamaKegiatan($hari, $ket) {
        if (str_contains($ket, '5S')) return 'Kegiatan 5S (Senyum, Salam, Sapa, Sopan, Santun)';
        if (str_contains($ket, 'Upacara')) return 'Upacara Bendera';
        if (str_contains($ket, 'Shalat')) return 'Shalat Dhuhur Berjamaah';
        if (str_contains($ket, 'Kokurikuler')) return 'Kegiatan Kokurikuler / Jum\'at Berseri';
        if (str_contains($ket, 'Pra-KBM')) {
            if ($hari == 'Selasa' || $hari == 'Kamis') return 'Pra-KBM: Senam';
            if ($hari == 'Rabu') return 'Pra-KBM: Literasi-Numerasi';
            return 'Pra-KBM: Kerohanian';
        }
        if ($hari == 'Sabtu' && str_contains($ket, 'Tadarus')) return 'Tadarus Juz Amma';
        return null;
    }
}