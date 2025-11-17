<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('m_perusahaan', function (Blueprint $table) {
            $table->text('deskripsi')->nullable()->after('website');
            $table->string('logo', 255)->nullable()->after('deskripsi');
            $table->string('gmaps', 255)->nullable()->after('logo');
        });
    }

    public function down(): void
    {
        Schema::table('m_perusahaan', function (Blueprint $table) {
            $table->dropColumn(['deskripsi', 'logo', 'gmaps']);
        });
    }
};