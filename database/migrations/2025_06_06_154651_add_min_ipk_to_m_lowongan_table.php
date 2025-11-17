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
        Schema::table('m_lowongan', function (Blueprint $table) {
            $table->decimal('min_ipk', 3, 2)->default(0.00)->after('kapasitas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m_lowongan', function (Blueprint $table) {
            $table->dropColumn('min_ipk');
        });
    }
};