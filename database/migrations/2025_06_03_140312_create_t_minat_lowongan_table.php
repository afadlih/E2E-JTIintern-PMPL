<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTMinatLowonganTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_minat_lowongan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_lowongan');
            $table->unsignedBigInteger('minat_id');
            $table->timestamps();
            
            $table->foreign('id_lowongan')->references('id_lowongan')->on('m_lowongan')
                  ->onDelete('cascade');
            $table->foreign('minat_id')->references('minat_id')->on('m_minat')
                  ->onDelete('cascade');
                  
            // Add unique constraint to prevent duplicate entries
            $table->unique(['id_lowongan', 'minat_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_minat_lowongan');
    }
}
