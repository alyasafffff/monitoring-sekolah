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
    Schema::create('jam_pelajaran_config', function (Blueprint $table) {
        $table->id();

        // Ganti ENUM jadi string biar fleksibel
        $table->string('hari_grup'); 
        // contoh isi: Senin, Reguler, Jumat, Ramadan, dll

        // Urutan jam (0 = literasi, 1, 2, dst)
        $table->integer('jam_ke');

        // Waktu mulai dan selesai (sumber waktu resmi sekolah)
        $table->time('jam_mulai');
        $table->time('jam_selesai');

        // Jenis slot (penting untuk presensi)
        $table->enum('tipe', ['mapel', 'istirahat', 'kegiatan'])
              ->default('mapel');

        // Keterangan tambahan (Upacara, Shalat, dll)
        $table->string('keterangan')->nullable();

        // Status aktif (kalau suatu saat ada perubahan pola)
        $table->boolean('is_active')->default(true);

        $table->timestamps();

        // Supaya tidak ada jam_ke dobel di grup yang sama
        $table->unique(['hari_grup', 'jam_ke']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jam_pelajaran_config');
    }
};
