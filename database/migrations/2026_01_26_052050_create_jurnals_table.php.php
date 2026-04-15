<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jurnals', function (Blueprint $table) {
            $table->id();
            
            // 1. Relasi ke Jadwal (Jadwal ini punya Guru Asli)
            $table->foreignId('jadwal_id')->constrained('jadwal_pelajaran')->cascadeOnDelete();
            
            // 2. Relasi ke Guru Pengisi (Siapa yang benar-benar mengajar saat itu)
            // Kita kasih nullable() agar jika guru asli yang mengajar, kita bisa fleksibel (atau tetap diisi ID guru asli)
            $table->foreignId('guru_id')->constrained('users')->cascadeOnDelete();
            
            $table->date('tanggal'); 
            $table->text('materi')->nullable(); 
            $table->text('catatan')->nullable(); 
            
            // Status Kehadiran GURU yang mengajar (Hadir/Izin/Sakit)
            $table->enum('status_guru', ['Hadir', 'Izin', 'Sakit'])->default('Hadir');

            // Status Pengisian Jurnal (proses = sedang absen, selesai = sudah simpan materi)
            $table->enum('status_pengisian', ['proses', 'selesai'])->default('proses');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jurnals');
    }
};