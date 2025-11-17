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
            // Check if column exists before dropping
            if (Schema::hasColumn('m_lowongan', 'skill_id')) {
                // First drop the foreign key constraint if it exists
                $table->dropForeign(['skill_id']);

                // Then drop the column
                $table->dropColumn('skill_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m_lowongan', function (Blueprint $table) {
            // Add back the column
            $table->unsignedBigInteger('skill_id')->nullable();

            // Add back the foreign key if needed
            $table->foreign('skill_id')->references('skill_id')->on('m_skill')->onDelete('set null');
        });
    }
};
