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
        Schema::table('schools', function (Blueprint $table) {
            // Add composite indexes for common query patterns
            $table->index(['education_level', 'status'], 'idx_education_status');
            $table->index(['kecamatan', 'education_level'], 'idx_kecamatan_education');
            $table->index(['status', 'created_at'], 'idx_status_created');

            // Add index for soft delete queries
            $table->index(['deleted_at', 'npsn'], 'idx_deleted_npsn');

            // Add index for email lookups
            $table->index('email', 'idx_email');

            // Add index for headmaster searches
            $table->index('headmaster', 'idx_headmaster');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropIndex('idx_education_status');
            $table->dropIndex('idx_kecamatan_education');
            $table->dropIndex('idx_status_created');
            $table->dropIndex('idx_deleted_npsn');
            $table->dropIndex('idx_email');
            $table->dropIndex('idx_headmaster');
        });
    }
};
