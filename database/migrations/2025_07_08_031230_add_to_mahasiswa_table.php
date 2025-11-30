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
        Schema::table('m_mahasiswa', function (Blueprint $table) {
            if (!Schema::hasColumn('m_mahasiswa', 'cv_path')) {
                $table->string('cv_path')->nullable()->after('cv');
            }
            if (!Schema::hasColumn('m_mahasiswa', 'cv_updated_at')) {
                $table->timestamp('cv_updated_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mahasiswa', function (Blueprint $table) {
            $table->dropColumn(['cv_path', 'cv_updated_at']);
        });
    }
};
