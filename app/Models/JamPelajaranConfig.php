<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class JamPelajaranConfig extends Model
{
    use HasFactory;

    // Nama tabel sesuai migration tadi
    protected $table = 'jam_pelajaran_config';

    // Kolom yang boleh diisi (Mass Assignment)
    protected $fillable = [
        'hari_grup',
        'jam_ke',
        'jam_mulai',
        'jam_selesai',
        'keterangan'
    ];

    /**
     * Fungsi Helper untuk menghitung durasi per jam pelajaran dalam menit.
     * Sangat berguna untuk logika akumulasi kehadiranmu nanti.
     */
    public function getDurasiMenitAttribute()
    {
        $mulai = Carbon::parse($this->jam_mulai);
        $selesai = Carbon::parse($this->jam_selesai);

        return $mulai->diffInMinutes($selesai);
    }
}