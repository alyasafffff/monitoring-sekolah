<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kegiatan extends Model
{
    // Karena Laravel mencari 'kegiatans' secara default, kita paksa ke 'kegiatan'
    protected $table = 'kegiatan'; 

    protected $fillable = ['nama_kegiatan', 'deskripsi'];
}