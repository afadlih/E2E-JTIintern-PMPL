<?php
// ✅ BUAT: database/migrations/2025_06_10_120000_add_completion_columns_to_m_magang_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('m_magang', function (Blueprint $table) {
            // ✅ ADD: Kolom yang missing
            $table->timestamp('completed_at')->nullable()->after('tgl_selesai');
            $table->string('completed_by')->nullable()->after('completed_at');
            $table->text('catatan_penyelesaian')->nullable()->after('completed_by');
            
            // ✅ ADD: Index untuk performa
            $table->index(['status', 'tgl_selesai'], 'idx_status_tgl_selesai');
            $table->index(['id_mahasiswa', 'status'], 'idx_mahasiswa_status');
        });
    }

    public function down(): void
    {
        Schema::table('m_magang', function (Blueprint $table) {
            $table->dropIndex('idx_status_tgl_selesai');
            $table->dropIndex('idx_mahasiswa_status');
            $table->dropColumn(['completed_at', 'completed_by', 'catatan_penyelesaian']);
        });
    }
};