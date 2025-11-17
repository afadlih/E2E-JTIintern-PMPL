<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('m_dosen', function (Blueprint $table) {
            // Drop existing perusahaan_id column
            $table->dropForeign(['perusahaan_id']);
            $table->dropColumn('perusahaan_id');
            
            // Add wilayah_id column
            $table->foreignId('wilayah_id')->nullable()->after('user_id')
                  ->constrained('m_wilayah', 'wilayah_id')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('m_dosen', function (Blueprint $table) {
            $table->dropForeign(['wilayah_id']);
            $table->dropColumn('wilayah_id');
            
            $table->foreignId('perusahaan_id')->nullable()->after('user_id')
                  ->constrained('m_perusahaan', 'perusahaan_id')
                  ->onDelete('set null');
        });
    }
};