<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('izin_siswa', function (Blueprint $table) {
            $table->id();
            
            // Relasi Siswa & Wali Kelas
            $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
            $table->foreignId('wali_kelas_id')->constrained('users')->cascadeOnDelete();
            
            // Info Izin
            $table->date('tanggal_izin');
            
            // Status utama
            $table->enum('status', ['Sakit', 'Izin', 'Dispensasi']);
            
            // --- TAMBAHAN KOLOM KETERANGAN ---
            // Kita pakai 'text' agar muat panjang, dan 'nullable' karena opsional
            $table->text('keterangan')->nullable(); 
            
            // (Opsional) Jika fitur jam izin parsial mau diaktifkan nanti
            $table->time('jam_mulai')->nullable();
            $table->time('jam_selesai')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('izin_siswa');
    }
};