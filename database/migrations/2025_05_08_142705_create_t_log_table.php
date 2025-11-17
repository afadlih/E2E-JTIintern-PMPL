<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_log', function (Blueprint $table) {
            $table->id('id_log'); // Primary key
            $table->foreignId('id_magang')->constrained('m_magang', 'id_magang')->onDelete('cascade');
            $table->date('tanggal');
            $table->text('log_aktivitas');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_log');
    }
};