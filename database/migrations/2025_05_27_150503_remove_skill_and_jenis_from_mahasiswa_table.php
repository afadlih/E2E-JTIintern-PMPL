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
        Schema::table('m_mahasiswa', function (Blueprint $table) {
            // Coba drop foreign key menggunakan nama konvensi Laravel
            try {
                $table->dropForeign(['skill_id']);
            } catch (\Exception $e) {
                // Foreign key mungkin tidak ada, lanjutkan
            }
            
            try {
                $table->dropForeign(['jenis_id']);
            } catch (\Exception $e) {
                // Foreign key mungkin tidak ada, lanjutkan
            }
            
            // Drop kolom
            if (Schema::hasColumn('m_mahasiswa', 'skill_id')) {
                $table->dropColumn('skill_id');
            }
            
            if (Schema::hasColumn('m_mahasiswa', 'jenis_id')) {
                $table->dropColumn('jenis_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m_mahasiswa', function (Blueprint $table) {
            // Add the columns back if needed for rollback
            if (!Schema::hasColumn('m_mahasiswa', 'skill_id')) {
                $table->unsignedBigInteger('skill_id')->nullable();
                $table->foreign('skill_id')->references('skill_id')->on('m_skill')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('m_mahasiswa', 'jenis_id')) {
                $table->unsignedBigInteger('jenis_id')->nullable();
                $table->foreign('jenis_id')->references('jenis_id')->on('m_jenis')->onDelete('set null');
            }
        });
    }
};