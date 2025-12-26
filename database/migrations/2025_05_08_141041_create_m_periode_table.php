<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_periode', function (Blueprint $table) {
            $table->id('periode_id'); // Primary key
            $table->string('waktu', 255);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_periode');
    }
};