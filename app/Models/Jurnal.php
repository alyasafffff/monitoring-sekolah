<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jurnal extends Model
{
    use HasFactory;

    protected $table = 'jurnals'; // Nama tabel di database
    protected $guarded = [];      // Biar semua kolom bisa diisi (mass assignment)

    // Relasi: Setiap Jurnal "milik" satu Jadwal
    public function jadwal()
    {
        return $this->belongsTo(JadwalPelajaran::class, 'jadwal_id');
    }
}