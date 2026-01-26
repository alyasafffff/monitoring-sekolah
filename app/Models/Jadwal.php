<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    use HasFactory;

    // Beritahu Laravel nama tabel aslinya
    protected $table = 'jadwal_pelajaran';
}