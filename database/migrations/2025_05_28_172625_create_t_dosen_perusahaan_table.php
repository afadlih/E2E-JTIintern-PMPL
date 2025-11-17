<?php
// filepath: database/migrations/2025_05_28_create_t_dosen_perusahaan_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTDosenPerusahaanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_dosen_perusahaan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_dosen');
            $table->unsignedBigInteger('perusahaan_id');
            $table->timestamps();

            $table->foreign('id_dosen')
                  ->references('id_dosen')
                  ->on('m_dosen')
                  ->onDelete('cascade');
                  
            $table->foreign('perusahaan_id')
                  ->references('perusahaan_id')
                  ->on('m_perusahaan')
                  ->onDelete('cascade');
                  
            $table->unique(['id_dosen', 'perusahaan_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_dosen_perusahaan');
    }
}