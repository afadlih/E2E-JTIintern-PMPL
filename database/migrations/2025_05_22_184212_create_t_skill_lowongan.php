<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('t_skill_lowongan', function (Blueprint $table) {
            $table->foreignId('id_lowongan')->constrained('m_lowongan', 'id_lowongan')->onDelete('cascade');
            $table->foreignId('id_skill')->constrained('m_skill', 'skill_id')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_skill_lowongan');
    }
};
