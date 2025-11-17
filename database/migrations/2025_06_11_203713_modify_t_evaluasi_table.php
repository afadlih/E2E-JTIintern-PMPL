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
        Schema::table('t_evaluasi', function (Blueprint $table) {
            // Rename existing columns
            $table->renameColumn('nilai', 'nilai_dosen');
            $table->renameColumn('eval', 'catatan_dosen');
            
            // Add new columns for company evaluation
            $table->decimal('nilai_perusahaan', 5, 2)->nullable()->after('id_magang');
            $table->decimal('nilai_akhir', 5, 2)->nullable()->after('nilai_perusahaan');
            $table->string('grade', 2)->nullable()->after('nilai_akhir');
            $table->string('file_penilaian_perusahaan')->nullable()->after('grade');
            $table->enum('status_evaluasi', ['pending', 'submitted_perusahaan', 'completed'])->default('pending')->after('file_penilaian_perusahaan');
            $table->datetime('tanggal_submit_perusahaan')->nullable()->after('status_evaluasi');
            $table->datetime('tanggal_evaluasi_dosen')->nullable()->after('tanggal_submit_perusahaan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_evaluasi', function (Blueprint $table) {
            // Remove new columns
            $table->dropColumn([
                'nilai_perusahaan',
                'nilai_akhir', 
                'grade',
                'file_penilaian_perusahaan',
                'status_evaluasi',
                'tanggal_submit_perusahaan',
                'tanggal_evaluasi_dosen'
            ]);
            
            // Rename back to original
            $table->renameColumn('nilai_dosen', 'nilai');
            $table->renameColumn('catatan_dosen', 'eval');
        });
    }
};
