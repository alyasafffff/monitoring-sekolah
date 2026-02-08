<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IzinSiswa extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'izin_siswa';

    // Kolom yang boleh diisi (Mass Assignment)
    protected $fillable = [
        'siswa_id',
        'wali_kelas_id',
        'tanggal_izin',
        'status',       // Sakit, Izin, Dispensasi
        'keterangan',   // <--- PENTING: Kolom baru
        'jam_mulai',    // Nullable
        'jam_selesai',  // Nullable
    ];
}