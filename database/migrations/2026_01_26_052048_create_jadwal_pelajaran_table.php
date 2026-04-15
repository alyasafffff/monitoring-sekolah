<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_pelajaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            
            // PERUBAHAN 1: Buat mapel_id nullable (boleh kosong) karena slot bisa diisi kegiatan
            $table->foreignId('mapel_id')->nullable()->constrained('mata_pelajaran')->cascadeOnDelete();
            
            // PERUBAHAN 2: Tambahkan foreignId untuk kegiatan (nullable juga)
            $table->foreignId('kegiatan_id')->nullable()->constrained('kegiatan')->cascadeOnDelete();
            
            $table->foreignId('guru_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('jam_pelajaran_config_id')->constrained('jam_pelajaran_config')->cascadeOnDelete();
            
            $table->string('hari'); 

            $table->timestamps();
            
            // Nama index manual agar tidak "Too Long"
            $table->unique(['kelas_id', 'jam_pelajaran_config_id', 'hari'], 'jadwal_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_pelajaran');
    }
};