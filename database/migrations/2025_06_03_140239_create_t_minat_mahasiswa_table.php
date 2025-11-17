<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTMinatMahasiswaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_minat_mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mahasiswa_id');
            $table->unsignedBigInteger('minat_id');
            $table->timestamps();
            
            $table->foreign('mahasiswa_id')->references('id_mahasiswa')->on('m_mahasiswa')
                  ->onDelete('cascade');
            $table->foreign('minat_id')->references('minat_id')->on('m_minat')
                  ->onDelete('cascade');
                  
            // Add unique constraint to prevent duplicate entries
            $table->unique(['mahasiswa_id', 'minat_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_minat_mahasiswa');
    }
}