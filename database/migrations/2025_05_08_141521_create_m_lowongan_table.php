<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_lowongan', function (Blueprint $table) {
            $table->id('id_lowongan'); // Primary key
            $table->foreignId('perusahaan_id')->constrained('m_perusahaan', 'perusahaan_id')->onDelete('cascade');
            $table->foreignId('periode_id')->constrained('m_periode', 'periode_id')->onDelete('cascade');
            $table->foreignId('jenis_id')->nullable()->constrained('m_jenis', 'jenis_id')->onDelete('set null');
            $table->string('judul_lowongan', 50);
            $table->string('nama_lowongan', 100)->nullable();
            $table->integer('kapasitas');
            $table->integer('kuota')->nullable();
            $table->float('min_ipk', 5, 2)->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('status', 20)->default('aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_lowongan');
    }
};
