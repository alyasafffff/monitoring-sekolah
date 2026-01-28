<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('presensi_detail', function (Blueprint $table) {
            $table->id();
            
            // 1. RELASI KE JURNAL (Untuk Pengelompokan Sesi)
            // Kalau Jurnal dihapus guru, absennya otomatis hilang.
            $table->foreignId('jurnal_id')->constrained('jurnals')->cascadeOnDelete();
            
            // 2. RELASI KE JADWAL (Untuk Shortcut Data Akademik)
            // Biar BK bisa langsung tau Mapel, Kelas, & Guru tanpa join ke jurnal dulu.
            $table->foreignId('jadwal_id')->constrained('jadwal_pelajaran')->cascadeOnDelete();
            
            // 3. DATA SISWA
            $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
            
            // 4. DATA PELENGKAP
            $table->date('tanggal'); // Tetap simpan tanggal biar query makin cepat
            $table->enum('status', ['Hadir', 'Alpha', 'Sakit', 'Izin'])->default('Hadir');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presensi_detail');
    }
};