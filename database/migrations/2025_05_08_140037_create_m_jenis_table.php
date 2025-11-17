<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_jenis', function (Blueprint $table) {
            $table->id('jenis_id'); // Primary key
            $table->string('nama_jenis', 255);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_jenis');
    }
};
