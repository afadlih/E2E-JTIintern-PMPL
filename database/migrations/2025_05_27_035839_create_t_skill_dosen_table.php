<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('t_skill_dosen', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('skill_id');
            $table->string('lama_skill')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id_user')->on('m_user')->onDelete('cascade');
            $table->foreign('skill_id')->references('skill_id')->on('m_skill')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('t_skill_dosen');
    }
};