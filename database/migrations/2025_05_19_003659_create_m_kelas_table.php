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
        Schema::create('m_kelas', function (Blueprint $table) {
            $table->id('id_kelas');
            $table->string('nama_kelas', 10);
            $table->string('kode_prodi', 5);
            $table->year('tahun_masuk');
            $table->timestamps();

            $table->foreign('kode_prodi')
                  ->references('kode_prodi')
                  ->on('m_prodi')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_kelas');
    }
};
