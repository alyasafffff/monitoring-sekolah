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
        Schema::create('jurnals', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke Jadwal
            $table->foreignId('jadwal_id')->constrained('jadwal_pelajaran')->cascadeOnDelete();
            
            $table->date('tanggal'); 
            
            // MATERI (Inti Pembelajaran)
            $table->text('materi')->nullable(); 

            // CATATAN TAMBAHAN (Masalah Fasilitas/Siswa/Lainnya)
            // Kita taruh setelah materi, sifatnya nullable (boleh kosong)
            $table->text('catatan')->nullable(); 
            
            // Status Kehadiran GURU
            $table->enum('status_guru', ['Hadir', 'Izin', 'Sakit'])->default('Hadir');

            // Status Pengisian Jurnal
            $table->enum('status_pengisian', ['proses', 'selesai'])->default('proses');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jurnals');
    }
};