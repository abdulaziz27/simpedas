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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->string('full_name');
            $table->string('nisn', 20)->unique();
            $table->string('nis', 20)->nullable();
            $table->string('birth_place', 100);
            $table->date('birth_date');
            $table->enum('gender', ['Laki-laki', 'Perempuan']);
            $table->enum('religion', ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu']);
            $table->string('grade_level', 20);
            $table->string('major', 100)->nullable();
            $table->text('achievements')->nullable();
            $table->enum('student_status', ['Aktif', 'Tamat', 'Pindah', 'Keluar']);
            $table->enum('graduation_status', ['Belum Lulus', 'Lulus', 'Tidak Lulus'])->nullable();
            $table->string('academic_year', 20);
            $table->timestamps();

            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->index(['school_id', 'student_status']);
            $table->index('full_name');
            $table->index('nisn');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
