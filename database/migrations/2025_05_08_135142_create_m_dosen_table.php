<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_dosen', function (Blueprint $table) {
            $table->id('id_dosen'); // Primary key
            $table->foreignId('user_id')->constrained('m_user', 'id_user')->onDelete('cascade'); // FK ke m_user
            $table->integer('nip')->unique(); // NIP unik
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_dosen');
    }
};