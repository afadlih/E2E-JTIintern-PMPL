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
        Schema::create('m_notifikasi', function (Blueprint $table) {
            $table->id('id_notifikasi');
            $table->unsignedBigInteger('id_user');
            $table->string('judul');
            $table->text('pesan');
            $table->enum('kategori', [
                'lamaran',
                'magang',
                'sistem',
                'pengumuman',
                'evaluasi',
                'deadline'
            ])->default('sistem');
            $table->boolean('is_read')->default(false);
            $table->boolean('is_important')->default(false);
            $table->json('metadata')->nullable();
            $table->timestamps();

            // Indexes untuk performance
            $table->index(['id_user', 'is_read']);
            $table->index(['id_user', 'created_at']);
            $table->index('kategori');

            // Foreign key constraint
            $table->foreign('id_user')->references('id_user')->on('m_user')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_notifikasi');
    }
};
