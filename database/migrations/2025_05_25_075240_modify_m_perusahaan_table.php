<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('m_perusahaan', function (Blueprint $table) {
            // Drop existing kota column
            $table->dropColumn('kota');
            
            // Add wilayah_id column
            $table->foreignId('wilayah_id')->nullable()->after('alamat_perusahaan')
                  ->constrained('m_wilayah', 'wilayah_id')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('m_perusahaan', function (Blueprint $table) {
            $table->dropForeign(['wilayah_id']);
            $table->dropColumn('wilayah_id');
            $table->string('kota', 50)->nullable()->after('alamat_perusahaan');
        });
    }
};