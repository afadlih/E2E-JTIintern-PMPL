<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('t_kapasitas_lowongan', function (Blueprint $table) {
            $table->id('id_kapasitas');
            $table->unsignedBigInteger('id_lowongan');
            $table->integer('kapasitas_tersedia');
            $table->integer('kapasitas_total');
            $table->timestamps();

            $table->foreign('id_lowongan')
                  ->references('id_lowongan')
                  ->on('m_lowongan')
                  ->onDelete('cascade');
        });

        // Populate table with existing lowongan data
        DB::statement('
            INSERT INTO t_kapasitas_lowongan (id_lowongan, kapasitas_tersedia, kapasitas_total, created_at, updated_at)
            SELECT id_lowongan, kapasitas, kapasitas, NOW(), NOW()
            FROM m_lowongan
        ');

        // Reduce available capacity for existing accepted applications
        DB::statement('
            UPDATE t_kapasitas_lowongan kl
            JOIN (
                SELECT l.id_lowongan, COUNT(*) as count
                FROM m_magang m
                JOIN m_lowongan l ON m.id_lowongan = l.id_lowongan
                WHERE m.status = "diterima"
                GROUP BY l.id_lowongan
            ) counts ON kl.id_lowongan = counts.id_lowongan
            SET kl.kapasitas_tersedia = kl.kapasitas_total - counts.count
            WHERE kl.kapasitas_tersedia >= counts.count
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_kapasitas_lowongan');
    }
};