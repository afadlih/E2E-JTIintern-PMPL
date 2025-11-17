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
        Schema::table('m_dosen', function (Blueprint $table) {
     
            // Tambah kolom perusahaan_id
            $table->unsignedBigInteger('perusahaan_id')->nullable()->after('user_id');
            $table->foreign('perusahaan_id')
                  ->references('perusahaan_id')
                  ->on('m_perusahaan')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m_dosen', function (Blueprint $table) {
            $table->dropForeign(['perusahaan_id']);
            $table->dropColumn('perusahaan_id');
            
            // Kembalikan kolom program_studi_id
            $table->string('program_studi_id')->nullable();
            $table->foreign('program_studi_id')
                  ->references('kode_prodi')
                  ->on('m_prodi')
                  ->onDelete('set null');
        });
    }
};
