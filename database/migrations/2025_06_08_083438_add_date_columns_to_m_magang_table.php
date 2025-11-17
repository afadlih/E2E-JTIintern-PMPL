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
        Schema::table('m_magang', function (Blueprint $table) {
            $table->date('tgl_mulai')->nullable()->after('status');
            $table->date('tgl_selesai')->nullable()->after('tgl_mulai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m_magang', function (Blueprint $table) {
            $table->dropColumn(['tgl_mulai', 'tgl_selesai']);
        });
    }
};