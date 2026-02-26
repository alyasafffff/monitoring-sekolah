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
    $table->foreignId('mapel_id')->constrained('mata_pelajaran')->cascadeOnDelete();
    $table->foreignId('guru_id')->constrained('users')->cascadeOnDelete();
    $table->foreignId('jam_pelajaran_config_id')->constrained('jam_pelajaran_config')->cascadeOnDelete();
    
    $table->string('hari'); // <--- PASTIKAN BARIS INI ADA

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