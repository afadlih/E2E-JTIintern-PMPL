<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTPlottingHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_plotting_riwayat', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('id_magang');
            $table->unsignedBigInteger('id_dosen');
            $table->float('score')->comment('SAW score');
            $table->float('wilayah_score');
            $table->float('skill_score');
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamps();

            $table->foreign('id_magang')
                  ->references('id_magang')
                  ->on('m_magang')
                  ->onDelete('cascade');
                  
            $table->foreign('id_dosen')
                  ->references('id_dosen')
                  ->on('m_dosen')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_plotting_riwayat');
    }
}