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
    Schema::create('log_pelanggaran_bk', function (Blueprint $table) {
        $table->id();
        $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
        $table->foreignId('jadwal_id')->constrained('jadwal_pelajaran')->cascadeOnDelete();
        $table->date('tanggal');
        $table->time('jam_kejadian'); // Jam saat terdeteksi Alpha
        // Status apakah sudah dipanggil/dibina oleh BK
        $table->enum('status_penanganan', ['Belum', 'Sudah'])->default('Belum');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_pelanggaran_bk');
    }
};
