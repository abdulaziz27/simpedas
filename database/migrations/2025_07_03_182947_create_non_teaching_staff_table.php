<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('non_teaching_staff', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->string('full_name');
            $table->string('nip_nik', 20)->nullable();
            $table->string('nuptk', 20)->nullable();
            $table->string('birth_place', 100);
            $table->date('birth_date');
            $table->enum('gender', ['Laki-laki', 'Perempuan']);
            $table->enum('religion', ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu']);
            $table->text('address');
            $table->string('email')->nullable();
            $table->string('staff_type', 100)->nullable();
            $table->string('position', 100);
            $table->string('education_level', 100);
            $table->string('education_major', 100)->nullable();
            $table->enum('employment_status', ['PNS', 'PPPK', 'PTY', 'Kontrak', 'Honorer']);
            $table->string('rank', 50)->nullable();
            $table->date('tmt')->nullable();
            $table->enum('status', ['Aktif', 'Tidak Aktif']);
            $table->string('assignment_letter_file')->nullable();
            $table->timestamps();

            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->index(['school_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('non_teaching_staff');
    }
};
