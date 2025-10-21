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
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            
            // === DAPODIK FIELDS ===
            $table->string('full_name'); // Nama
            $table->string('nuptk', 20)->unique()->nullable(); // NUPTK
            $table->string('gender', 1); // JK (L/P)
            $table->string('birth_place', 100)->nullable(); // Tempat Lahir
            $table->date('birth_date')->nullable(); // Tanggal Lahir
            $table->string('nip', 20)->nullable(); // NIP
            $table->string('employment_status')->nullable(); // Status Kepegawaian
            $table->string('jenis_ptk')->nullable(); // Jenis PTK
            $table->string('gelar_depan')->nullable(); // Gelar Depan
            $table->string('gelar_belakang')->nullable(); // Gelar Belakang
            $table->string('jenjang')->nullable(); // Jenjang (S1, S2, S3, etc)
            $table->string('education_major')->nullable(); // Jurusan/Prodi
            $table->string('sertifikasi')->nullable(); // Sertifikasi
            $table->date('tmt')->nullable(); // TMT Kerja
            $table->text('tugas_tambahan')->nullable(); // Tugas Tambahan
            $table->text('mengajar')->nullable(); // Mengajar
            $table->integer('jam_tugas_tambahan')->nullable(); // Jam Tugas Tambahan
            $table->integer('jjm')->nullable(); // JJM
            $table->integer('total_jjm')->nullable(); // Total JJM
            $table->integer('siswa')->nullable(); // Siswa
            $table->text('kompetensi')->nullable(); // Kompetensi
            
            // === ADDITIONAL FIELDS (not in Dapodik but useful) ===
            $table->text('subjects')->nullable(); // Keep for backward compatibility
            $table->string('photo')->nullable(); // For manual photo upload
            $table->timestamps();

            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->index(['school_id']);
            $table->index('full_name');
            $table->index('nuptk');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
