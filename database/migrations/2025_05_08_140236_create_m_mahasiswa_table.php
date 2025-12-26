<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_mahasiswa', function (Blueprint $table) {
            $table->id('id_mahasiswa'); // Primary key
            $table->foreignId('id_user')->constrained('m_user', 'id_user')->onDelete('cascade');
            $table->string('kode_prodi'); // FK ke m_prodi
            $table->foreign('kode_prodi')->references('kode_prodi')->on('m_prodi')->onDelete('cascade');
            $table->foreignId('skill_id')->nullable()->constrained('m_skill', 'skill_id')->onDelete('set null');
            $table->foreignId('jenis_id')->nullable()->constrained('m_jenis', 'jenis_id')->onDelete('set null');
            $table->integer('nim')->unique();
            $table->string('alamat', 50)->nullable();
            $table->float('ipk', 5, 2)->nullable();
            $table->string('telp', 25)->nullable();
            $table->string('cv', 50)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_mahasiswa');
    }
};