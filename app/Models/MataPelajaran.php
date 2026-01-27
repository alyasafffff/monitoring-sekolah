<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MataPelajaran extends Model
{
    use HasFactory;

    // Karena nama tabel bukan jamak bahasa inggris (plural), kita definisikan manual:
    protected $table = 'mata_pelajaran';

    protected $fillable = [
        'kode_mapel', // <--- Jangan lupa daftarkan ini
        'nama_mapel',
    ];
}