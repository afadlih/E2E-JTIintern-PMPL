<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_prodi', function (Blueprint $table) {
            $table->string('kode_prodi')->primary(); // Primary key
            $table->string('nama_prodi');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_prodi');
    }
};