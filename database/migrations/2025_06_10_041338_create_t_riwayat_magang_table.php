<?php
// ✅ BUAT: database/migrations/2025_06_10_120001_create_t_riwayat_magang_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_riwayat_magang', function (Blueprint $table) {
            $table->id('id_riwayat');
            $table->foreignId('id_magang')->constrained('m_magang', 'id_magang')->onDelete('cascade');
            $table->foreignId('id_mahasiswa')->constrained('m_mahasiswa', 'id_mahasiswa')->onDelete('cascade');
            $table->foreignId('id_lowongan')->constrained('m_lowongan', 'id_lowongan')->onDelete('cascade');
            $table->foreignId('id_dosen')->nullable()->constrained('m_dosen', 'id_dosen')->onDelete('set null');
            
            // ✅ PERIODE MAGANG
            $table->date('tgl_mulai');
            $table->date('tgl_selesai');
            $table->integer('durasi_hari')->nullable();
            
            // ✅ STATUS DAN PENYELESAIAN
            $table->enum('status_awal', ['aktif', 'selesai', 'tidak aktif']);
            $table->enum('status_akhir', ['selesai', 'dibatalkan', 'expired']);
            $table->timestamp('completed_at');
            $table->enum('completed_by', ['system', 'admin', 'mahasiswa', 'dosen'])->default('system');
            $table->enum('status_completion', ['auto_completed', 'manual_completed', 'forced_completed']);
            
            // ✅ CATATAN
            $table->text('catatan_penyelesaian')->nullable();
            $table->decimal('nilai_akhir', 5, 2)->nullable();
            $table->enum('grade', ['A', 'B', 'C', 'D', 'E'])->nullable();
            $table->text('feedback_perusahaan')->nullable();
            $table->text('feedback_dosen')->nullable();
            
            $table->timestamps();
            
            // ✅ INDEXES
            $table->index(['id_mahasiswa', 'tgl_selesai']);
            $table->index(['status_akhir', 'completed_at']);
            $table->index(['completed_by', 'status_completion']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_riwayat_magang');
    }
};