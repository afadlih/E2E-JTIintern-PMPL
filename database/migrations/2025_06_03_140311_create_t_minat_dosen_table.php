<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTMinatDosenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_minat_dosen', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dosen_id');
            $table->unsignedBigInteger('minat_id');
            $table->timestamps();
            
            $table->foreign('dosen_id')->references('id_dosen')->on('m_dosen')
                  ->onDelete('cascade');
            $table->foreign('minat_id')->references('minat_id')->on('m_minat')
                  ->onDelete('cascade');
                  
            // Add unique constraint to prevent duplicate entries
            $table->unique(['dosen_id', 'minat_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_minat_dosen');
    }
}