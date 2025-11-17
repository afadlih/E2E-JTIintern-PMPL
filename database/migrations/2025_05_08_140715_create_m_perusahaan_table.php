<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_perusahaan', function (Blueprint $table) {
            $table->id('perusahaan_id'); // Primary key
            $table->string('nama_perusahaan', 50);
            $table->string('alamat_perusahaan', 50)->nullable();
            $table->string('kota', 50)->nullable();
            $table->string('contact_person', 50); // int(50) diubah ke string(50) agar bisa menyimpan nomor telepon
            $table->string('email', 255);
            $table->string('instagram', 255)->nullable();
            $table->string('website', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_perusahaan');
    }
};