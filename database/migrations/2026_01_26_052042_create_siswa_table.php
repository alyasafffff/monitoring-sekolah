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
        Schema::create('siswa', function (Blueprint $table) {
            $table->id();
            $table->string('nisn', 15)->unique();
            $table->string('nama_siswa');
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            // String acak untuk generate QR Code (biar tidak gampang dipalsukan)
            $table->string('qr_token', 60)->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswa');
    }
};
