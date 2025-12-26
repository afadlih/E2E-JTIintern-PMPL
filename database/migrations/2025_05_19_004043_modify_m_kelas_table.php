<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('m_mahasiswa', function (Blueprint $table) {
            if (Schema::hasColumn('m_mahasiswa', 'kode_prodi')) {
                // Drop foreign key first using Laravel naming convention
                $table->dropForeign(['kode_prodi']);
                // Then drop the column
                $table->dropColumn('kode_prodi');
            }
        });
    }

    public function down(): void
    {
        Schema::table('m_mahasiswa', function (Blueprint $table) {
            // Restore the kode_prodi column if it doesn't exist
            if (!Schema::hasColumn('m_mahasiswa', 'kode_prodi')) {
                $table->string('kode_prodi')->after('id_user');
                $table->foreign('kode_prodi')
                      ->references('kode_prodi')
                      ->on('m_prodi')
                      ->onDelete('cascade');
            }
        });
    }
};
