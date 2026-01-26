<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    use HasFactory;

    // Beritahu Laravel nama tabelnya
    protected $table = 'presensi';

    // Kolom yang diizinkan untuk diisi (Mass Assignment)
    protected $fillable = [
        'jadwal_id',
        'tanggal',
        'status',
    ];
}