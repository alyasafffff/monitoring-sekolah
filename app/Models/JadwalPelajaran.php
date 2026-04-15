<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalPelajaran extends Model
{
    use HasFactory;

    protected $table = 'jadwal_pelajaran';
    
    // Menggunakan guarded kosong agar semua kolom bisa diisi melalui controller
    protected $guarded = [];

    // 1. Relasi ke Guru (User)
    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    // 2. Relasi ke Kelas
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    // 3. Relasi ke Mata Pelajaran (Gunakan leftJoin/Nullable di logic karena bisa kosong)
    public function mapel()
    {
        return $this->belongsTo(MataPelajaran::class, 'mapel_id');
    }

    // 4. Tambahkan Relasi ke Kegiatan (PENTING!)
    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class, 'kegiatan_id');
    }

    // 5. Tambahkan Relasi ke Konfigurasi Jam
    public function jamConfig()
    {
        return $this->belongsTo(JamPelajaranConfig::class, 'jam_pelajaran_config_id');
    }
}