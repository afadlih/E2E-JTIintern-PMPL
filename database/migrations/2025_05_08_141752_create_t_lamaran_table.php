<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_lamaran', function (Blueprint $table) {
            $table->id('id_lamaran'); // Primary key
            $table->foreignId('id_lowongan')->constrained('m_lowongan', 'id_lowongan')->onDelete('cascade');
            $table->foreignId('id_mahasiswa')->constrained('m_mahasiswa', 'id_mahasiswa')->onDelete('cascade');
            $table->foreignId('id_dosen')->nullable()->constrained('m_dosen', 'id_dosen')->onDelete('set null');
            $table->foreignId('id_dokumen')->nullable()->constrained('m_dokumen', 'id_dokumen')->onDelete('set null');
            $table->enum('auth', ['menunggu', 'diterima', 'ditolak'])->default('menunggu');
            $table->date('tanggal_lamaran');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_lamaran');
    }
};