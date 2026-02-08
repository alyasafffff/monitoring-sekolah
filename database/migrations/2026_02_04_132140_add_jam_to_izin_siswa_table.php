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
        // Perhatikan: Kita pakai Schema::table (bukan create)
        // Artinya kita mau EDIT tabel yang sudah ada ('izin_siswa')
        Schema::table('izin_siswa', function (Blueprint $table) {
            
            // Tambah 2 kolom baru (Boleh Kosong / Nullable)
            // Kalau kosong = Izin Seharian
            // Kalau diisi = Izin Jam Tertentu
            $table->time('jam_mulai')->nullable()->after('status');
            $table->time('jam_selesai')->nullable()->after('jam_mulai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('izin_siswa', function (Blueprint $table) {
            // Hapus kolom kalau migrasi dibatalkan
            $table->dropColumn(['jam_mulai', 'jam_selesai']);
        });
    }
};