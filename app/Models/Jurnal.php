<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class JadwalPelajaran extends Model
{
    use HasFactory;

    // Pastikan nama tabel benar (karena di migration kamu pakai 'jadwal_pelajaran')
    protected $table = 'jadwal_pelajaran'; 

    protected $guarded = [];

    // --- RELASI INI WAJIB ADA ---
    
    // Relasi ke Guru (User)
    public function guru()
    {
        // Sesuaikan 'guru_id' dengan nama kolom di tabel jadwal_pelajaran
        return $this->belongsTo(User::class, 'guru_id'); 
    }

    // Relasi ke Kelas
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    // Relasi ke Mapel
    public function mapel()
    {
        return $this->belongsTo(MataPelajaran::class, 'mapel_id');
    }
}