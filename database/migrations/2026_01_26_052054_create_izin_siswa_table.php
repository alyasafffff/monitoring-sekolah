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

            // --- TAMBAHAN BARU: Kolom Foto ---
            // Kita gunakan string karena yang disimpan adalah path/nama filenya saja
            $table->string('bukti_foto')->nullable(); 
            
            // --- Menggunakan Jam Ke (Sesi) ---
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