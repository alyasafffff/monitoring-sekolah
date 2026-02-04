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
            $table->text('materi'); // <--- Ini yang menampung isi jurnal (String)
            
            // Status kehadiran
            $table->enum('status', ['Hadir', 'Izin', 'Sakit'])->default('Hadir');
            
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
