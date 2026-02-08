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
            
            // 1. RELASI KE JURNAL (Main Parent)
            // Cukup ini saja. Kalau mau tau Jadwal/Tanggal, cek via relasi Jurnal.
            $table->foreignId('jurnal_id')->constrained('jurnals')->cascadeOnDelete();
            
            // 2. DATA SISWA
            $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
            
            // 3. STATUS KEHADIRAN
            // Default Hadir. Nanti Guru tinggal ubah yang Sakit/Izin aja.
            $table->enum('status', ['Hadir', 'Alpha', 'Sakit', 'Izin'])->default('Hadir');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presensi_detail');
    }
};