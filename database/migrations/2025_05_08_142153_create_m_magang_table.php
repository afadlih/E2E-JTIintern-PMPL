<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_magang', function (Blueprint $table) {
            $table->id('id_magang'); // Primary key
            $table->foreignId('id_lowongan')->constrained('m_lowongan', 'id_lowongan')->onDelete('cascade');
            $table->foreignId('id_mahasiswa')->constrained('m_mahasiswa', 'id_mahasiswa')->onDelete('cascade');
            $table->foreignId('id_dosen')->nullable()->constrained('m_dosen', 'id_dosen')->onDelete('set null');
            $table->enum('status', ['aktif', 'selesai', 'tidak aktif'])->default('tidak aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_magang');
    }
};