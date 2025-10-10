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
        Schema::create('import_progress', function (Blueprint $table) {
            $table->id();
            $table->string('import_id')->unique();
            $table->unsignedBigInteger('user_id');
            $table->string('status')->default('not_started');
            $table->integer('total')->default(0);
            $table->integer('processed')->default(0);
            $table->integer('success')->default(0);
            $table->integer('failed')->default(0);
            $table->json('errors')->nullable();
            $table->json('warnings')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'import_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_progress');
    }
};
