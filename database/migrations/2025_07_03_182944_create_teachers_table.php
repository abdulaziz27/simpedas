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
            $table->string('full_name');
            $table->string('nuptk', 20)->unique()->nullable();
            $table->string('nip', 20)->nullable();
            $table->string('birth_place', 100)->nullable();
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['Laki-laki', 'Perempuan']);
            $table->enum('religion', ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'])->nullable();
            $table->text('address')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('education_level', 100)->nullable();
            $table->string('education_major', 100)->nullable();
            $table->text('subjects')->nullable(); // JSON atau comma separated
            $table->enum('employment_status', ['PNS', 'PPPK', 'GTY', 'PTY'])->nullable();
            $table->string('rank', 50)->nullable();
            $table->string('position', 100)->nullable();
            $table->date('tmt')->nullable(); // Tanggal Mulai Tugas
            $table->enum('status', ['Aktif', 'Tidak Aktif', 'Pensiun']);
            $table->string('academic_year', 20)->nullable();
            $table->string('photo')->nullable();
            $table->timestamps();

            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->index(['school_id', 'status']);
            $table->index('full_name');
            $table->index('nuptk');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
