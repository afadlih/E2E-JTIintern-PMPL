<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Get the actual foreign key name
        $foreignKey = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = '" . env('DB_DATABASE') . "' 
            AND TABLE_NAME = 'm_mahasiswa' 
            AND COLUMN_NAME = 'kode_prodi' 
            AND REFERENCED_TABLE_NAME = 'm_prodi'
        ");

        Schema::table('m_mahasiswa', function (Blueprint $table) use ($foreignKey) {
            if (Schema::hasColumn('m_mahasiswa', 'kode_prodi')) {
                if (!empty($foreignKey)) {
                    $table->dropForeign($foreignKey[0]->CONSTRAINT_NAME);
                }
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