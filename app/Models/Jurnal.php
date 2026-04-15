<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jurnal extends Model
{
    use HasFactory;

    protected $table = 'jurnals'; 
    protected $guarded = [];      

    // 1. Relasi ke Jadwal (Jadwal ini punya Guru yang "Seharusnya" mengajar)
    public function jadwal()
    {
        return $this->belongsTo(JadwalPelajaran::class, 'jadwal_id');
    }

    // 2. Relasi ke Guru Pengisi (Siapa yang "Benar-benar" mengajar saat itu)
    // Kita sebut relasinya 'pengisi' agar tidak tertukar dengan guru di jadwal
    public function pengisi()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    /**
     * Helper: Cek apakah kelas ini diambil alih oleh orang lain (Piket)
     */
    public function isDiambilAlih()
    {
        // Jika guru_id di jurnal berbeda dengan guru_id di jadwal asli
        return $this->guru_id !== $this->jadwal->guru_id;
    }
}