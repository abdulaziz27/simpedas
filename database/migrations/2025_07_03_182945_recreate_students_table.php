<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop foreign key constraints first (only if tables exist)
        if (Schema::hasTable('student_certificates')) {
            Schema::table('student_certificates', function (Blueprint $table) {
                $table->dropForeign(['student_id']);
            });
        }

        if (Schema::hasTable('student_reports')) {
            Schema::table('student_reports', function (Blueprint $table) {
                $table->dropForeign(['student_id']);
            });
        }

        Schema::dropIfExists('students');

        Schema::create('students', function (Blueprint $table) {
            $table->id();

            // ==== W A J I B ====
            $table->string('nisn', 20)->unique();                  // Wajib: NISN (unik nasional)
            $table->string('nipd', 20)->nullable();                // Opsional: NIPD (tidak semua sekolah pakai)
            $table->string('nama_lengkap', 150);                   // Wajib: Nama lengkap
            $table->enum('jenis_kelamin', ['L', 'P']);              // Wajib: L/P
            $table->string('tempat_lahir', 100);                   // Wajib
            $table->date('tanggal_lahir');                         // Wajib
            $table->string('agama', 50);                           // Wajib
            $table->string('rombel', 50);                          // Wajib: Rombongan belajar
            $table->foreignId('sekolah_id')                        // Wajib: relasi ke tabel schools
                ->constrained('schools')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            // Tambahan opsional
            $table->string('foto', 255)->nullable();               // Path/nama file foto

            // ==== O P S I O N A L ====
            // Domisili
            $table->text('alamat')->nullable();
            $table->string('kelurahan', 100)->nullable();
            $table->string('kecamatan', 100)->nullable();
            $table->string('kode_pos', 10)->nullable();

            // Status
            $table->enum('status_siswa', ['aktif', 'tamat', 'pindah'])
                ->default('aktif');

            // Data keluarga
            $table->string('nama_ayah', 150)->nullable();
            $table->string('pekerjaan_ayah', 100)->nullable();
            $table->string('nama_ibu', 150)->nullable();
            $table->string('pekerjaan_ibu', 100)->nullable();
            $table->unsignedSmallInteger('anak_ke')->nullable();
            $table->unsignedSmallInteger('jumlah_saudara')->nullable();

            // Kontak
            $table->string('no_hp', 20)->nullable();

            // Sosial-ekonomi
            $table->boolean('kip')->nullable();
            $table->string('transportasi', 50)->nullable();
            $table->decimal('jarak_rumah_sekolah', 5, 2)->nullable();

            // Kesehatan
            $table->unsignedSmallInteger('tinggi_badan')->nullable();
            $table->unsignedSmallInteger('berat_badan')->nullable();

            // Index untuk pencarian & laporan
            $table->index('sekolah_id');
            $table->index('rombel');
            $table->index('status_siswa');

            $table->timestamps();
        });

        // Recreate foreign key constraints (only if tables exist)
        if (Schema::hasTable('student_certificates')) {
            Schema::table('student_certificates', function (Blueprint $table) {
                $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            });
        }

        if (Schema::hasTable('student_reports')) {
            Schema::table('student_reports', function (Blueprint $table) {
                $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
