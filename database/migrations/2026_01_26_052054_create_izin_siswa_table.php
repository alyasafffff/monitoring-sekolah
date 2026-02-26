<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('izin_siswa', function (Blueprint $table) {
            $table->id();
            
            // Relasi Siswa & Wali Kelas
            $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
            $table->foreignId('wali_kelas_id')->constrained('users')->cascadeOnDelete();
            
            // Info Izin
            $table->date('tanggal_izin');
            $table->enum('status', ['Sakit', 'Izin', 'Dispensasi']);
            $table->text('keterangan')->nullable(); 
            
            // --- PERUBAHAN: Menggunakan Jam Ke (Sesi) ---
            // Jika jam_ke_mulai NULL, sistem menganggap izin SEHARIAN (Full Day)
            $table->integer('jam_ke_mulai')->nullable();
            $table->integer('jam_ke_selesai')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('izin_siswa');
    }
};