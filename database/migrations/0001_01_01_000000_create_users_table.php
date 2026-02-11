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
            // 1. Tabel Users (Kustom sesuai kebutuhan Sekolah)
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('nip', 20)->unique(); // Login pakai NIP
                $table->string('name');
                $table->string('password');
                $table->enum('role', ['admin', 'guru', 'bk', 'kepsek']);

                // Data Tambahan
                $table->string('no_hp', 15)->nullable();
                $table->string('foto_profil')->nullable();
                $table->boolean('is_active')->default(true);

                $table->rememberToken(); // <-- Fitur 'Ingat Saya' (JANGAN DIHAPUS)
                $table->timestamps();
            });

            // 2. Tabel Password Reset Tokens (Bawaan Laravel)
            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });

            // 3. Tabel Sessions (PENTING: Ini solusi error 500 kamu)
            Schema::create('sessions', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->foreignId('user_id')->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload');
                $table->integer('last_activity')->index();
            });
        }   

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::dropIfExists('users');
            Schema::dropIfExists('password_reset_tokens');
            Schema::dropIfExists('sessions');
        }
    };