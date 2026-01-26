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
    Schema::create('presensi_detail', function (Blueprint $table) {
        $table->id();
        $table->foreignId('jadwal_id')->constrained('jadwal_pelajaran')->cascadeOnDelete();
        $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
        $table->date('tanggal');
        $table->enum('status', ['Hadir', 'Alpha', 'Sakit', 'Izin']); // Sakit/Izin otomatis dari tab 6
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presensi_detail');
    }
};
