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
        Schema::create('t_skill_mahasiswa', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('m_user', 'id_user')->onDelete('cascade'); // Foreign key ke tabel m_user (id_user)
            $table->foreignId('skill_id')->constrained('m_skill', 'skill_id')->onDelete('cascade'); // Foreign key ke tabel m_skill (skill_id)
            $table->integer('lama_skill')->nullable(); // Lama skill dalam satuan waktu (misalnya bulan/tahun)
            $table->primary(['user_id', 'skill_id']); // Composite primary key
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_skill_mahasiswa');
    }
};