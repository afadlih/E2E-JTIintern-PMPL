<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_evaluasi', function (Blueprint $table) {
            $table->id('id_evaluasi'); // Primary key
            $table->foreignId('id_magang')->constrained('m_magang', 'id_magang')->onDelete('cascade');
            $table->integer('nilai')->nullable();
            $table->text('eval')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_evaluasi');
    }
};